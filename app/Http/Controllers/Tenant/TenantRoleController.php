<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Tenant\PermissionNameEnum;
use Illuminate\Http\Request;
use App\Models\Spatie\Role;
use App\Models\Spatie\Permission;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TenantRoleController extends BaseTenantController
{
  /**
   * 角色管理頁面
   */
  public function index(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::角色管理);

    if ($request->ajax()) {
      $records = Role::latest()->get();

    return DataTables::of($records)
      ->addColumn('role_name',        fn($record) => $record->name)
      ->addColumn('role_permissions', fn($record) => $record->permissions->pluck('name')
          ->map(fn($name) => PermissionNameEnum::getDisplayName($name))->toArray())
      ->make(true);
    }

    return view('content.tenant.setting.tenant-role');
  }

  /**
   * 顯示新增角色表單
   */
  public function create()
  {
    $this->authorizePermission(PermissionNameEnum::角色管理);

    return view('content.tenant.setting.tenant-role-add', [
      'permissions' => Permission::all(),
    ]);
  }

  /**
   * 創建角色
   */
  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::角色管理);

    try {
      $attributes = $request->validate([
        'name'          => 'required|string|max:255',
        'permissions'   => 'nullable|array',
        'permissions.*' => 'string',
      ],[],[
        'name'          => '角色名稱',
        'permissions'   => '權限',
      ]);

      $role = Role::create([
        'name'        => $attributes['name'],
        'guard_name'  => 'tenant',
      ]);

      if (!empty($attributes['permissions'])) {
        $role->syncPermissions($attributes['permissions']);
      }

      return $this->successResponse('角色新增成功', ['redirect_url' => route('tenant.roles.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('角色新增失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 顯示角色資料
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNameEnum::角色管理);

    $role = Role::findOrFail($id);

    return $this->successResponse('角色取得成功', ['role' => $role], 200);
  }

  /**
   * 顯示編輯角色表單
   */
  public function edit($id)
  {
    $this->authorizePermission(PermissionNameEnum::角色管理);

    $role = Role::findOrFail($id);

    return view('content.tenant.setting.tenant-role-add', [
      'role'        => $role,
      'permissions' => Permission::all(),
    ]);
  }

  /**
   * 更新角色
   */
  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNameEnum::角色管理);

    try {
      $attributes = $request->validate([
        'name'          => 'required|string|max:255',
        'permissions'   => 'nullable|array',
        'permissions.*' => 'string',
        ],[],[
        'name'          => '角色名稱',
        'permissions'   => '權限',
      ]);

      $role = Role::findOrFail($id);

      $role->update([
        'name' => $attributes['name'],
      ]);

      if (isset($attributes['permissions'])) {
        $role->syncPermissions($attributes['permissions']);
      } else {
        $role->syncPermissions([]);
      }

      return $this->successResponse('角色更新成功', ['redirect_url' => route('tenant.roles.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('角色更新失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 刪除角色
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNameEnum::角色管理);

    try {
      $role = Role::findOrFail($id);

      $role->delete();

      return $this->successResponse('角色刪除成功', ['redirect_url' => route('tenant.roles.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('角色刪除失敗，請聯絡管理者', null, 500);
    }
  }
}
