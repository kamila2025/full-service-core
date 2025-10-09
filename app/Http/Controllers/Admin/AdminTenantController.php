<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Admin\Tenant\TenantStatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Admin\tenantService;
use Yajra\DataTables\Facades\DataTables;

class AdminTenantController extends Controller
{
  protected $tenantService;

  public function __construct(tenantService $tenantService)
  {
    $this->tenantService = $tenantService;
  }

  /**
   * 租戶管理頁面
   */
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $query = $this->tenantService->searchTenant($request);

      $records = $query->orderBy('sort', 'asc')->get();

      return DataTables::of($records)
        ->addColumn('tenant_id',            fn($record) => $record->id)
        ->addColumn('tenant_name',          fn($record) => $record->name)
        ->addColumn('tenant_expire_date',   fn($record) => $record->expire_date)
        ->addColumn('tenant_status',        fn($record) => TenantStatusEnum::from($record->status)->label())
        ->addColumn('tenant_status_badge',  fn($record) => TenantStatusEnum::from($record->status)->badge())
        ->addColumn('tenant_user_name',     fn($record) => $record->user->name)
        ->addColumn('tenant_created_at',    fn($record) => $record->created_at->format('Y-m-d H:i:s'))
        ->make(true);
    }

    return view('content.admin.admin-tenants', [
      'tenants' => Tenant::all(),
      'users'   => User::all(),
    ]);
  }

  /**
   * 匯出租戶資料
   */
  public function export(Request $request)
  {
    try {
      return $this->tenantService->exportTenants($request);
    } catch (\Throwable $e) {
      return $this->errorResponse('匯出失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 顯示創建租戶表單
   */
  public function create()
  {
    $tenantId = $this->tenantService->generateUniqueTenantId(6);

    return view('content.admin.admin-tenants-add', ['tenantId' => $tenantId]);
  }

  /**
   * 創建租戶
   */
  public function store(Request $request)
  {
    try {
      $attributes = $request->validate([
        'id'           => 'required|string|max:12',
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|max:255',
        'password'     => 'required|string|min:5',
        'expire_date'  => 'required|date',
        'status'       => 'required|string|in:activated,unactivated',
      ], [], [
        'id'           => '租戶ID',
        'name'         => '租戶名稱',
        'email'        => '租戶信箱',
        'password'     => '租戶密碼',
        'expire_date'  => '到期時間',
        'status'       => '租戶狀態',
      ]);

      // 創建租戶與租戶管理員
      $this->tenantService->createTenant($attributes);

      return $this->successResponse('租戶新增成功', ['redirect_url' => route('admin.tenants.index')], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('租戶新增失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 顯示編輯租戶表單
   */
  public function edit($id)
  {
    $tenant = Tenant::findOrFail($id);

    return view('content.admin.admin-tenants-add', ['tenant' => $tenant]);
  }

  /**
   * 更新租戶
   */
  public function update(Request $request, $id)
  {
    try {
      $attributes = $request->validate([
        'id'           => 'required|string|max:12',
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|max:255',
        'password'     => 'nullable|string|min:5',
        'expire_date'  => 'required|date',
        'status'       => 'required|string|in:activated,unactivated',
      ], [], [
        'id'           => '租戶ID',
        'name'         => '租戶名稱',
        'email'        => '租戶信箱',
        'password'     => '租戶密碼',
        'expire_date'  => '到期時間',
        'status'       => '租戶狀態',
      ]);

      // 更新租戶
      $this->tenantService->updateTenant($id, $attributes);

      return $this->successResponse('租戶更新成功', ['redirect_url' => route('admin.tenants.index')], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('租戶更新失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 刪除租戶
   */
  public function destroy($id)
  {
    try {
      $this->tenantService->deleteTenant($id);

      return $this->successResponse('租戶刪除成功', ['redirect_url' => route('admin.tenants.index')], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('租戶刪除失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 模擬登入租戶
   */
  public function impersonate($id)
  {
    if (auth()->check()) {
      // 讀取租戶資料
      $tenant = Tenant::findOrFail($id);

      // 租戶初始化
      tenancy()->initialize($tenant);

      // 讀取租戶使用者
      $user = User::where('tenant_id', $tenant->id)->first();

      // 租戶讀取結束
      tenancy()->end();

      // 創建 token
      $token = tenancy()->impersonate($tenant, $user->id, $redirectUrl = route('tenant.dashboard.index', ['tenant' => $tenant->id]), $authGuard = 'tenant');

      // 導向模擬登入路由
      return redirect("{$tenant->id}/impersonate/{$token->token}");
    } else {
      return $this->errorResponse('模擬登入失敗，請聯絡管理者', null, 500);
    }
  }
}
