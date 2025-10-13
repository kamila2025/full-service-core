<?php

namespace App\Http\Controllers\Tenant\Line;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\LineUser;
use App\Models\Tenant;
use App\Services\Admin\tenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantLineLoginController extends Controller
{
  public function __construct(private tenantService $tenantService) {}

  /**
   * Line Login 頁面
   */
  public function login()
  {
    $tenant = Tenant::findOrFail(tenant('id'));

    if (!$this->tenantService->isLineConfigured($tenant)) {
      return view('content.tenant.line.tenant-line-login-error', [
        'message' => '此網站尚未配置 Line 服務'
      ]);
    }

    // 生成 state 參數用於安全驗證
    $state = Str::random(32);
    session(['line_login_state' => $state]);

    // 構建 Line Login URL
    $loginUrl = $this->buildLineLoginUrl($tenant, $state);

    return view('content.tenant.line.tenant-line-login', [
      'tenant'    => $tenant,
      'loginUrl'  => $loginUrl,
      'state'     => $state
    ]);
  }

  /**
   * 處理 Line Login 回調
   */
  public function callback(Request $request)
  {
    $attributes = $request->validate([
      'code'              => 'required|string',
      'state'             => 'required|string',
      'error'             => 'nullable|string',
      'error_description' => 'nullable|string',
    ]);

    $tenant = Tenant::findOrFail(tenant('id'));

    if (!$this->tenantService->isLineConfigured($tenant)) {
      return view('content.tenant.line.tenant-line-login-error', [
        'message' => '此網站尚未配置 Line 服務'
      ]);
    }

    // 檢查錯誤
    if ($attributes['error']) {
      Log::error('Line Login 錯誤', [
        'tenant_id'         => $tenant->id,
        'error'             => $attributes['error'],
        'error_description' => $attributes['error_description']
      ]);

      return view('content.tenant.line.tenant-line-login-error', [
        'message' => 'Line 登入失敗：' . ($attributes['error_description'] ?? $attributes['error'])
      ]);
    }

    // 驗證 state 參數
    if (!$attributes['state'] || $attributes['state'] !== session('line_login_state')) {
      Log::warning('Line Login state 驗證失敗', [
        'tenant_id' => $tenant->id,
        'expected'  => session('line_login_state'),
        'received'  => $attributes['state']
      ]);

      return view('content.tenant.line.tenant-line-login-error', [
        'message' => '安全驗證失敗，請重新嘗試'
      ]);
    }

    // 清除 state
    session()->forget('line_login_state');

    try {
      // 取得 Line 使用者資料
      $lineUserData = $this->getLineUserData($tenant, $attributes['code']);

      if (!$lineUserData) {
        return view('content.tenant.line.tenant-line-login-error', [
          'message' => '無法取得 Line 使用者資料'
        ]);
      }

      // 檢查是否已經綁定
      $existingLineUser = LineUser::where('line_user_id', $lineUserData['userId'])->first();

      if ($existingLineUser) {
        // 已經綁定，顯示成功頁面
        return view('content.tenant.line.tenant-line-bind-success', [
          'tenant' => $tenant,
          'member' => $existingLineUser->member,
          'lineUser' => $existingLineUser,
          'isExisting' => true
        ]);
      }

      // 直接創建會員和 Line 使用者綁定
      try {
        DB::beginTransaction();

        // 創建會員（使用 Line 顯示名稱作為姓名，電話使用 Line User ID）
        $member = Member::create([
          'name' => $lineUserData['displayName'],
          'phone' => 'LINE_' . $lineUserData['userId'], // 使用 Line User ID 作為電話識別
          'email' => null, // Line 登入不提供 email
        ]);

        // 創建 Line 使用者綁定
        $lineUser = LineUser::create([
          'line_user_id' => $lineUserData['userId'],
          'member_id' => $member->id,
          'profile' => [
            'displayName' => $lineUserData['displayName'],
            'pictureUrl' => $lineUserData['pictureUrl'] ?? null,
            'statusMessage' => $lineUserData['statusMessage'] ?? null,
          ],
        ]);

        DB::commit();

        Log::info('Line 自動綁定成功', [
          'tenant_id' => $tenant->id,
          'line_user_id' => $lineUserData['userId'],
          'member_id' => $member->id
        ]);

        // 顯示成功頁面
        return view('content.tenant.line.tenant-line-bind-success', [
          'tenant' => $tenant,
          'member' => $member,
          'lineUser' => $lineUser,
          'isExisting' => false
        ]);
      } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Line 自動綁定失敗', [
          'tenant_id' => $tenant->id,
          'line_user_id' => $lineUserData['userId'],
          'error' => $e->getMessage()
        ]);

        return view('content.tenant.line.tenant-line-login-error', [
          'message' => '自動綁定失敗：' . $e->getMessage()
        ]);
      }
    } catch (\Exception $e) {
      Log::error('Line Login 處理失敗', [
        'tenant_id' => $tenant->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return view('content.tenant.line.tenant-line-login-error', [
        'message' => '處理 Line 登入時發生錯誤：' . $e->getMessage()
      ]);
    }
  }


  /**
   * 顯示綁定成功頁面
   */
  public function showSuccess(Request $request)
  {
    $tenant = tenant();
    $memberId = $request->get('member_id');
    $lineUserId = $request->get('line_user_id');

    $member = Member::find($memberId);
    $lineUser = LineUser::where('line_user_id', $lineUserId)->first();

    if (!$member || !$lineUser) {
      return view('content.tenant.line.tenant-line-login-error', [
        'message' => '找不到相關資料'
      ]);
    }

    return view('content.tenant.line.tenant-line-bind-success', [
      'tenant' => $tenant,
      'member' => $member,
      'lineUser' => $lineUser,
      'isExisting' => false
    ]);
  }

  /**
   * 構建 Line Login URL
   */
  private function buildLineLoginUrl(Tenant $tenant, string $state): string
  {
    $params = http_build_query([
      'client_id'     => $tenant->channel_id,
      'redirect_uri'  => route('tenant.line.login.callback', ['tenant' => $tenant->id]),
      'response_type' => 'code',
      'scope'         => 'profile openid email',
      'state'         => $state,
      'nonce'         => Str::random(16)
    ]);

    return 'https://access.line.me/oauth2/v2.1/authorize?' . $params;
  }

  /**
   * 取得 Line 使用者資料
   */
  private function getLineUserData(Tenant $tenant, string $code): ?array
  {
    // 取得 access token
    $tokenResponse = $this->getAccessToken($tenant, $code);

    if (!$tokenResponse) {
      return null;
    }

    // 取得使用者資料
    $userResponse = $this->getUserProfile($tokenResponse['access_token']);

    if (!$userResponse) {
      return null;
    }

    return [
      'userId'        => $userResponse['userId'],
      'displayName'   => $userResponse['displayName'],
      'pictureUrl'    => $userResponse['pictureUrl'] ?? null,
      'statusMessage' => $userResponse['statusMessage'] ?? null,
      'access_token'  => $tokenResponse['access_token'],
      'id_token'      => $tokenResponse['id_token'] ?? null,
    ];
  }

  /**
   * 交換授權碼取得 access token
   */
  private function getAccessToken(Tenant $tenant, string $code): ?array
  {
    $data = [
      'grant_type'    => 'authorization_code',
      'code'          => $code,
      'redirect_uri'  => route('tenant.line.login.callback', ['tenant' => $tenant->id]),
      'client_id'     => $tenant->channel_id,
      'client_secret' => $tenant->channel_secret,
    ];

    $response = $this->makeHttpRequest('https://api.line.me/oauth2/v2.1/token', $data);

    return $response ? json_decode($response, true) : null;
  }

  /**
   * 取得使用者資料
   */
  private function getUserProfile(string $accessToken): ?array
  {
    $headers = [
      'Authorization: Bearer ' . $accessToken
    ];

    $response = $this->makeHttpRequest('https://api.line.me/v2/profile', [], $headers);

    return $response ? json_decode($response, true) : null;
  }

  /**
   * 發送 HTTP 請求
   */
  private function makeHttpRequest(string $url, array $data = [], array $headers = []): ?string
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if (!empty($data)) {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    if (!empty($headers)) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode !== 200) {
      Log::error('Line API 請求失敗', [
        'url' => $url,
        'http_code' => $httpCode,
        'response' => $response
      ]);
      return null;
    }

    return $response;
  }
}
