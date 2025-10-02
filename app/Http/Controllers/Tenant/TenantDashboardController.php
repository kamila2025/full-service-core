<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TenantDashboardController extends Controller
{
  /**
   * 主控台頁面
   */
  function index()
  {
      return view('content.tenant.tenant-dashboard');
  }
}
