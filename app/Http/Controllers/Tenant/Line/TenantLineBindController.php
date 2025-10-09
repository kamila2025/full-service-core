<?php

namespace App\Http\Controllers\Tenant\Line;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\LineUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantLineBindController extends Controller
{
  /**
   * 顯示 Line 綁定表單
   */
  public function showBindForm(Request $request)
  {
    $tenant = tenant();

    // 檢查租戶是否已配置 Line
    if (!$this->isLineConfigured($tenant)) {
      return view('content.tenant.line.tenant-line-bind-error', [
        'message' => '此租戶尚未配置 Line 服務'
      ]);
    }

    $bindToken = $this->generateBindToken($tenant);

    return view('content.tenant.line.tenant-line-bind-form', [
      'tenant' => $tenant,
      'bindToken' => $bindToken
    ]);
  }

  /**
   * 處理 Line 綁定提交
   */
  public function submitBind(Request $request)
  {
    $tenant = tenant();

    // 檢查租戶是否已配置 Line
    if (!$this->isLineConfigured($tenant)) {
      return response()->json([
        'success' => false,
        'message' => '此租戶尚未配置 Line 服務'
      ], 400);
    }

    $data = $request->validate([
      'line_user_id' => 'required|string',
      'name' => 'required|string|max:50',
      'phone' => 'required|string|max:30',
      'email' => 'nullable|email|max:100',
      'bind_token' => 'required|string',
    ]);

    // 驗證綁定 token
    if (!$this->validateBindToken($tenant, $data['bind_token'])) {
      return response()->json([
        'success' => false,
        'message' => '無效的綁定請求'
      ], 400);
    }

    try {
      DB::beginTransaction();

      // 創建或更新會員資料
      $member = Member::updateOrCreate(
        ['phone' => $data['phone']],
        [
          'name' => $data['name'],
          'email' => $data['email'],
        ]
      );

      // 綁定 Line 用戶
      LineUser::updateOrCreate(
        ['line_user_id' => $data['line_user_id']],
        [
          'member_id' => $member->id,
        ]
      );

      DB::commit();

      Log::info('Line 綁定成功', [
        'tenant_id' => $tenant->id,
        'line_user_id' => $data['line_user_id'],
        'member_id' => $member->id
      ]);

      return response()->json([
        'success' => true,
        'message' => '綁定成功！',
        'member_id' => $member->id
      ]);
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('Line 綁定失敗', [
        'tenant_id' => $tenant->id,
        'line_user_id' => $data['line_user_id'],
        'error' => $e->getMessage()
      ]);

      return response()->json([
        'success' => false,
        'message' => '綁定失敗，請稍後再試'
      ], 500);
    }
  }

  /**
   * 檢查 Line 用戶是否已綁定
   */
  public function checkBinding(Request $request)
  {
    $tenant = tenant();
    $lineUserId = $request->input('line_user_id');

    if (!$lineUserId) {
      return response()->json([
        'bound' => false,
        'message' => '缺少 Line 用戶 ID'
      ], 400);
    }

    $lineUser = LineUser::where('line_user_id', $lineUserId)
      ->whereNotNull('member_id')
      ->with('member')
      ->first();

    if ($lineUser) {
      return response()->json([
        'bound' => true,
        'member' => $lineUser->member
      ]);
    }

    return response()->json([
      'bound' => false
    ]);
  }

  /**
   * 檢查租戶是否已配置 Line
   */
  private function isLineConfigured($tenant): bool
  {
    $config = $tenant->line_config;

    return $config &&
      isset($config['channel_id'], $config['channel_access_token'], $config['channel_secret']) &&
      $config['status'] === 'enabled';
  }

  /**
   * 生成綁定 token
   */
  private function generateBindToken($tenant): string
  {
    $token = Str::random(32);

    // 將 token 存到 session 中，有效期 10 分鐘
    session()->put("line_bind_token_{$tenant->id}", [
      'token' => $token,
      'expires_at' => now()->addMinutes(10)
    ]);

    return $token;
  }

  /**
   * 驗證綁定 token
   */
  private function validateBindToken($tenant, string $token): bool
  {
    $sessionKey = "line_bind_token_{$tenant->id}";
    $sessionData = session()->get($sessionKey);

    if (!$sessionData || $sessionData['token'] !== $token) {
      return false;
    }

    if (now()->isAfter($sessionData['expires_at'])) {
      session()->forget($sessionKey);
      return false;
    }

    return true;
  }
}
