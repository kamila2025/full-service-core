<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\TenantPermissionSeeder;

class tenantService
{
    /**
     * 創建租戶及其管理員
     */
    public function createTenant(array $attributes): Tenant
    {
      try {
          // 創建租戶
          $tenant = Tenant::create([
            'id'              => $attributes['id'],
            'name'            => $attributes['name'],
            'user_id'         => Auth::user()->id,
            'email'           => $attributes['email'],
            'password'        => Hash::make($attributes['password']),
            'expire_date'     => $attributes['expire_date'],
            'status'          => $attributes['status'],
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
                'id'              => $attributes['id'],
                'name'            => $attributes['name'],
                'user_id'         => Auth::user()->id,
                'email'           => $attributes['email'],
                'expire_date'     => $attributes['expire_date'],
                'status'          => $attributes['status'],
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
     * 執行租戶 seeder
     */
    private function runTenantPermissionSeeder(): void
    {
        $seeder = new TenantPermissionSeeder();
        $seeder->run();
    }
}
