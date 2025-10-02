<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class TenantPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清除快取
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 建立權限
        $permissions = [
            'manage' => '所有權限',
            'products.interface' => '商品介面',
            'products' => '商品管理',
            'categories' => '分類管理',
            'settings.interface' => '設定介面',
            'users' => '員工管理',
            'roles' => '角色管理',
        ];

        foreach ($permissions as $name => $display_name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'tenant',
            ]);
        }

        // 建立角色
        $roles = [
            'admin' => [
                'name' => '管理員',
                'permissions' => [
                    'manage',
                    'products.interface',
                    'products',
                    'categories',
                    'settings.interface',
                    'users',
                    'roles',
                ]
            ],
        ];

        foreach ($roles as $key => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $key,
                'guard_name' => 'tenant',
            ]);

            // 分配權限給角色
            if (!empty($roleData['permissions'])) {
                $role->syncPermissions($roleData['permissions']);
            }
        }

        // 為第一個使用者分配管理員角色
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->assignRole('admin');
        }
    }
}
