<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Admin\Tenant\TenantStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

class TenantAuthController extends Controller
{
  /**
   * 租戶登入頁面
   */
  function index()
  {
    $tenant = Tenant::findOrFail(tenant('id'));

    if (Auth::guard('tenant')->check()) {
      return redirect()->route('tenant.dashboard.index', ['tenant' => tenant('id')]);
    }

    return view('content.tenant.tenant-login', [
      'tenant'      => $tenant,
      'pageConfigs' => ['myLayout' => 'blank'],
    ]);
  }

  /**
   * 租戶登入
   */
  function login(Request $request)
  {
    try {
      $attributes = $request->validate(
        [
          'email'     => 'required|string|email|max:255',
          'password'  => 'required|string|min:5',
        ],
        [],
        [
          'email'     => '帳號',
          'password'  => '密碼',
        ]
      );

      $tenant = Tenant::findOrFail(tenant('id'));

      if ($tenant->status === TenantStatusEnum::未開通->value) {
        return $this->errorResponse('網站未開通，請聯絡管理者', null, 401);
      }

      if ($tenant->expire_date < now()) {
        return $this->errorResponse('網站已到期，請聯絡管理者', null, 401);
      }


      if (Auth::guard('tenant')->attempt($attributes)) {
        $request->session()->regenerate();

        $tenantId = tenant('id');

        return $this->successResponse('登入成功', ['redirect_url' => route('tenant.dashboard.index', ['tenant' => $tenantId])], 200);
      }

      return $this->errorResponse('帳號或密碼錯誤', null, 401);
    } catch (\Throwable $e) {
      return $this->errorResponse($e->getMessage(), null, 500);
    }
  }

  /**
   * 租戶登出
   */
  function logout()
  {
    $tenantId = tenant('id');

    Artisan::call('permission:cache-reset');

    Auth::guard('tenant')->logout();

    return redirect(route('tenant.login.index', ['tenant' => $tenantId]));
  }
}
