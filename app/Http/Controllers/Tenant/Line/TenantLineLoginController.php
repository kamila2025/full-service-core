<?php

namespace App\Http\Controllers\Tenant\Line;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\LineUser;
use App\Models\Tenant;
use App\Services\Admin\TenantService;
use App\Services\Tenant\Line\LineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantLineLoginController extends Controller
{
  public function __construct(private TenantService $tenantService, private LineService $lineService) {}

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
    $loginUrl = $this->lineService->buildLineLoginUrl($tenant, $state);

    return view('content.tenant.line.tenant-line-login', [
      'tenant'      => $tenant,
      'loginUrl'    => $loginUrl,
      'state'       => $state,
      'pageConfigs' => ['myLayout' => 'blank'],
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
    if (isset($attributes['error']) && $attributes['error']) {
      Log::error('Line Login 錯誤', [
        'tenant_id'         => $tenant->id,
        'error'             => $attributes['error'],
        'error_description' => $attributes['error_description'] ?? null
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
      $lineUserData = $this->lineService->getLineUserData($tenant, $attributes['code']);

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
          'tenant'      => $tenant,
          'member'      => $existingLineUser->member,
          'lineUser'    => $existingLineUser,
          'isExisting'  => true
        ]);
      }

      // 直接創建會員和 Line 使用者綁定
      try {
        DB::beginTransaction();

        // 創建會員
        $member = Member::create([
          'name'  => $lineUserData['displayName'],
        ]);

        // 創建 Line 使用者綁定
        $lineUser = LineUser::create([
          'line_user_id'  => $lineUserData['userId'],
          'member_id'     => $member->id,
          'profile' => [
            'displayName'   => $lineUserData['displayName'],
            'pictureUrl'    => $lineUserData['pictureUrl'] ?? null,
            'statusMessage' => $lineUserData['statusMessage'] ?? null,
          ],
        ]);

        DB::commit();

        Log::info('Line 綁定成功', [
          'tenant_id'     => $tenant->id,
          'line_user_id'  => $lineUserData['userId'],
          'member_id'     => $member->id
        ]);

        // 顯示成功頁面
        return view('content.tenant.line.tenant-line-bind-success', [
          'tenant'      => $tenant,
          'member'      => $member,
          'lineUser'    => $lineUser,
          'isExisting'  => false
        ]);
      } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('Line 自動綁定失敗', [
          'tenant_id'     => $tenant->id,
          'line_user_id'  => $lineUserData['userId'],
          'error'         => $e->getMessage()
        ]);

        return view('content.tenant.line.tenant-line-login-error', [
          'message' => '自動綁定失敗：' . $e->getMessage()
        ]);
      }
    } catch (\Throwable $e) {
      Log::error('Line Login 處理失敗', [
        'tenant_id' => $tenant->id,
        'error'     => $e->getMessage(),
        'trace'     => $e->getTraceAsString()
      ]);

      return view('content.tenant.line.tenant-line-login-error', [
        'message' => '處理 Line 登入時發生錯誤：' . $e->getMessage()
      ]);
    }
  }
}
