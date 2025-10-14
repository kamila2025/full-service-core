<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Tenant\PermissionNameEnum;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\Tenant\UserService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class TenantUserController extends BaseTenantController
{
  public function __construct(protected UserService $userService) {}

  /**
   * 員工管理頁面
   */
  public function index(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    if ($request->ajax()) {
      $records = User::latest()->get();

      return DataTables::of($records)
        ->addColumn('user_name',       fn($record) => $record->name)
        ->addColumn('user_email',      fn($record) => $record->email)
        ->addColumn('user_created_at', fn($record) => $record->created_at->format('Y-m-d H:i:s'))
        ->addColumn('user_isAdmin',    fn($record) => $record->parameter['isAdmin'] ?? false)
        ->make(true);
    }

    return view('content.tenant.setting.tenant-user');
  }

  /**
   * 顯示新增員工表單
   */
  public function create()
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    return view('content.tenant.setting.tenant-user-add', [
      'roles'       => Role::all(),
      'permissions' => Permission::all(),
    ]);
  }

  /**
   * 創建員工
   */
  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    try {
      $attributes = $request->validate([
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|max:255',
        'password'      => 'required|string|min:5',
        'role'          => 'nullable|string',
        'permissions'   => 'nullable|array',
        'permissions.*' => 'string',
      ], [], [
        'name'          => '員工姓名',
        'email'         => '員工信箱',
        'password'      => '員工密碼',
        'role'          => '員工角色',
        'permissions'   => '權限',
      ]);

      $user = $this->userService->createUser($attributes);

      return $this->successResponse('員工新增成功', ['redirect_url' => route('tenant.users.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('員工新增失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 顯示員工資料
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    $user = User::findOrFail($id);

    return $this->successResponse('員工取得成功', ['user' => $user], 200);
  }

  /**
   * 顯示編輯員工表單
   */
  public function edit($id)
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    $user = User::findOrFail($id);

    return view('content.tenant.setting.tenant-user-add', [
      'user'        => $user,
      'roles'       => Role::all(),
      'permissions' => Permission::all(),
    ]);
  }

  /**
   * 更新員工
   */
  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    try {
      $attributes = $request->validate([
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|max:255',
        'password'     => 'nullable|string|min:5',
        'role'         => 'nullable|string',
        'permissions'  => 'nullable|array',
        'permissions.*' => 'string',
      ], [], [
        'name'         => '員工姓名',
        'email'        => '員工信箱',
        'password'     => '員工密碼',
        'role'         => '員工角色',
        'permissions'  => '權限',
      ]);

      $user = $this->userService->updateUser($id, $attributes);

      return $this->successResponse('員工更新成功', ['redirect_url' => route('tenant.users.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('員工更新失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 刪除員工
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    try {
      $this->userService->deleteUser($id);

      return $this->successResponse('員工刪除成功', ['redirect_url' => route('tenant.users.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('員工刪除失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 獲取角色權限
   */
  public function getRolePermissions($roleName)
  {
    $this->authorizePermission(PermissionNameEnum::員工管理);

    try {
      $role = Role::where('name', $roleName)->first();

      if (!$role) {
        return $this->errorResponse('角色不存在', null, 404);
      }

      $permissions = $role->permissions->pluck('name')->toArray();

      return $this->successResponse('角色權限取得成功', ['permissions' => $permissions], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('獲取角色權限失敗，請聯絡管理者', null, 500);
    }
  }
}
