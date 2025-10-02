<?php

namespace App\Console\Commands\Tenant;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class AddTenantPermissionCommand extends Command
{
    use TenantAwareCommand, HasATenantsOption;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:permission:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新增租戶權限到資料庫';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('請輸入權限名稱');
        $guard = 'tenant';

        // 檢查權限是否已存在
        $existingPermission = Permission::where('name', $name)
            ->where('guard_name', $guard)
            ->first();

        if ($existingPermission) {
            $this->warn("權限 '{$name}' 已存在於 guard '{$guard}' 中");
            return;
        }

        try {
            // 建立權限
            $permission = Permission::create([
                'name' => $name,
                'guard_name' => $guard,
            ]);

            $this->info("成功建立租戶權限: {$name}");

            // 清除權限快取
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $this->info("已清除權限快取");

        } catch (\Exception $e) {
            $this->error("建立權限時發生錯誤: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
