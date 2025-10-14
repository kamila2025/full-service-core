<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Database\Seeders\TenantPermissionSeeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\TenantExport;

class TenantService
{
  /**
   * 創建租戶及其管理員
   */
  public function createTenant(array $attributes): Tenant
  {
    try {
      // 創建租戶
      $tenant = Tenant::create([
        'id'                    => $attributes['id'],
        'name'                  => $attributes['name'],
        'user_id'               => Auth::user()->id,
        'email'                 => $attributes['email'],
        'password'              => Hash::make($attributes['password']),
        'expire_date'           => $attributes['expire_date'],
        'status'                => $attributes['status'],
        'channel_id'            => $attributes['line_channel_id'],
        'channel_secret'        => $attributes['line_channel_secret'],
        'channel_access_token'  => $attributes['line_channel_access_token'],
      ]);

      // 創建租戶管理員
      $tenant->run(function () use ($attributes, $tenant) {
        User::create([
          'tenant_id' => $tenant->id,
          'name'      => $attributes['name'] . '管理員',
          'email'     => $attributes['email'],
          'password'  => Hash::make($attributes['password']),
          'parameter' => ['isAdmin' => true]
        ]);

        // 執行租戶 seeder
        $this->runTenantPermissionSeeder();
      });

      return $tenant;
    } catch (\Throwable $e) {
      throw $e;
    }
  }

  /**
   * 更新租戶及其管理員
   */
  public function updateTenant($id, array $attributes): Tenant
  {
    try {
      $tenant = Tenant::findOrFail($id);

      $updateData = [
        'id'                    => $attributes['id'],
        'name'                  => $attributes['name'],
        'user_id'               => Auth::user()->id,
        'email'                 => $attributes['email'],
        'expire_date'           => $attributes['expire_date'],
        'status'                => $attributes['status'],
        'channel_id'            => $attributes['line_channel_id'],
        'channel_secret'        => $attributes['line_channel_secret'],
        'channel_access_token'  => $attributes['line_channel_access_token'],
      ];

      // 只有在有密碼時才更新密碼
      if (isset($attributes['password']) && !empty($attributes['password'])) {
        $updateData['password'] = Hash::make($attributes['password']);
      }

      $tenant->update($updateData);

      // 更新租戶管理員
      $tenant->run(function () use ($attributes, $tenant) {
        $adminUser = User::where('tenant_id', $tenant->id)->first();
        if ($adminUser) {
          $adminUpdateData = [
            'name'  => $attributes['name'] . '管理員',
            'email' => $attributes['email'],
          ];

          // 只有在有密碼時才更新密碼
          if (isset($attributes['password']) && !empty($attributes['password'])) {
            $adminUpdateData['password'] = Hash::make($attributes['password']);
          }

          $adminUser->update($adminUpdateData);
        }
      });

      return $tenant;
    } catch (\Throwable $e) {
      throw $e;
    }
  }

  /**
   * 刪除租戶
   */
  public function deleteTenant($id): void
  {
    try {
      $tenant = Tenant::findOrFail($id);

      $tenant->delete();
    } catch (\Throwable $e) {
      throw $e;
    }
  }

  /**
   * 生成唯一的租戶ID
   */
  public function generateUniqueTenantId(int $length = 6): string
  {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';

    do {
      $tenantId = substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
    } while (Tenant::where('id', $tenantId)->exists());

    return $tenantId;
  }

  /**
   * 建構租戶搜尋查詢
   */
  public function searchTenant(Request $request)
  {
    $query = Tenant::query();

    // 租戶ID搜尋
    if ($request->filled('searchId')) {
      $query->where('id', 'like', '%' . $request->searchId . '%');
    }

    // 租戶名稱搜尋
    if ($request->filled('searchName')) {
      $query->where('name', 'like', '%' . $request->searchName . '%');
    }

    // 管理人員搜尋
    if ($request->filled('searchUser')) {
      $query->where('user_id', $request->searchUser);
    }

    // 租戶狀態搜尋
    if ($request->filled('searchStatus')) {
      $query->where('data->status', $request->searchStatus);
    }

    // 到期時間範圍搜尋
    if ($request->filled('searchExpireStartDate')) {
      $query->whereDate('data->expire_date', '>=', $request->searchExpireStartDate);
    }

    if ($request->filled('searchExpireEndDate')) {
      $query->whereDate('data->expire_date', '<=', $request->searchExpireEndDate);
    }

    // 建立時間範圍搜尋
    if ($request->filled('searchCreatedStartDate')) {
      $query->whereDate('created_at', '>=', $request->searchCreatedStartDate);
    }

    if ($request->filled('searchCreatedEndDate')) {
      $query->whereDate('created_at', '<=', $request->searchCreatedEndDate);
    }

    return $query;
  }

  /**
   * 匯出租戶資料
   */
  public function exportTenants(Request $request)
  {
    $query = $this->searchTenant($request);
    $tenants = $query->orderBy('sort', 'asc')->get();

    // 生成檔案名稱
    $timestamp = now()->format('Y-m-d');
    $filename = "租戶資料_{$timestamp}.xlsx";

    return Excel::download(new TenantExport($tenants), $filename);
  }

  /**
   * 執行租戶 seeder
   */
  private function runTenantPermissionSeeder(): void
  {
    $seeder = new TenantPermissionSeeder();
    $seeder->run();
  }

  /**
   * 判斷有無設定 Line Channel ID 和 Channel Secret
   */
  public function isLineConfigured(Tenant $tenant): bool
  {
    $lineConfig = isset($tenant->channel_id) && isset($tenant->channel_secret);

    return $lineConfig;
  }
}
