<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAuthController extends Controller
{
  /**
   * 登入頁面
   */
  function index()
  {
    if (Auth::guard('web')->check()) {
      return redirect()->route('admin.dashboard.index');
    }

    return view('content.admin.admin-login', [
      'pageConfigs' => ['myLayout' => 'blank']
    ]);
  }

  /**
   * 登入
   */
  function login(Request $request)
  {
    try {
      $attributes = $request->validate([
          'email'     => 'required|string|email|max:255',
          'password'  => 'required|string|min:5',
      ],
      [],
      [
          'email'     => '帳號',
          'password'  => '密碼',
      ]);

      if (Auth::attempt($attributes)) {
          $request->session()->regenerate();

          return $this->successResponse('登入成功', ['redirect_url' => route('admin.dashboard.index')], 200);
      }

      return $this->errorResponse('帳號或密碼錯誤', null, 401);
    } catch (\Throwable $e) {
      return $this->errorResponse($e->getMessage(), null, 500);
    }
  }

  /**
   * 登出
   */
  function logout()
  {
    Auth::guard('web')->logout();

    return redirect()->route('admin.login.index');
  }
}
