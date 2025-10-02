<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
  /**
   * 主控台頁面
   */
  function index()
  {
      return view('content.admin.admin-dashboard');
  }
}
