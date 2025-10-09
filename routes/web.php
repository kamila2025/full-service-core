<?php

use Illuminate\Support\Facades\Route;

/**
 * 管理後台路由
 */
Route::middleware(['web'])->group(function () {
  Route::prefix('admin')->middleware([])->group(function () {
    // Auth
    Route::get('/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'index'])->name('admin.login.index');
    Route::post('/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('/logout', [\App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('admin.logout');

    // 管理後台
    Route::middleware(['auth:web'])->group(function () {
      Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard.index');

      // 租戶匯出資料
      Route::get('/tenants/export', [\App\Http\Controllers\Admin\AdminTenantController::class, 'export'])->name('admin.tenants.export');
      // 模擬登入租戶
      Route::get('/tenants/{id}/impersonate', [\App\Http\Controllers\Admin\AdminTenantController::class, 'impersonate'])->name('admin.impersonate.login');;
      // 租戶管理
      Route::resource('tenants', \App\Http\Controllers\Admin\AdminTenantController::class)->names('admin.tenants');
    });
  });
});

/**
 * 租戶路由
 */
Route::group([
  'prefix' => '/{tenant}',
  'middleware' => [
    'web',
    Stancl\Tenancy\Middleware\InitializeTenancyByPath::class,
  ],
], function () {
  // 模擬租戶登入
  Route::get('/impersonate/{token}', function ($token) {
    return Stancl\Tenancy\Features\UserImpersonation::makeResponse($token);
  })->name('tenants.impersonate.login');

  Route::prefix('admin')->middleware([])->group(function () {
    // Auth
    Route::get('/login', [\App\Http\Controllers\Tenant\TenantAuthController::class, 'index'])->name('tenant.login.index');
    Route::post('/login', [\App\Http\Controllers\Tenant\TenantAuthController::class, 'login'])->name('tenant.login');
    Route::post('/logout', [\App\Http\Controllers\Tenant\TenantAuthController::class, 'logout'])->name('tenant.logout');

    // 租戶後台
    Route::middleware(['auth.tenant'])->group(function () {
      Route::get('/', [\App\Http\Controllers\Tenant\TenantDashboardController::class, 'index'])->name('tenant.dashboard.index');
      Route::get('/dashboard', [\App\Http\Controllers\Tenant\TenantDashboardController::class, 'index'])->name('tenant.dashboard.index');

      // 商品管理
      Route::post('categories/sort', [\App\Http\Controllers\Tenant\TenantCategoryController::class, 'updateSort'])->name('tenant.categories.sort');
      Route::get('categories/tree', [\App\Http\Controllers\Tenant\TenantCategoryController::class, 'getCategoryTree'])->name('tenant.categories.tree');
      Route::resource('categories', \App\Http\Controllers\Tenant\TenantCategoryController::class)->names('tenant.categories');

      Route::delete('products/images/{id}', [\App\Http\Controllers\Tenant\TenantProductController::class, 'destroyImage'])->name('tenant.products.images.destroy');
      Route::resource('products', \App\Http\Controllers\Tenant\TenantProductController::class)->names('tenant.products');

      // 員工管理
      Route::resource('users', \App\Http\Controllers\Tenant\TenantUserController::class)->names('tenant.users');
      Route::get('users/role/{roleName}/permissions', [\App\Http\Controllers\Tenant\TenantUserController::class, 'getRolePermissions'])->name('tenant.users.role.permissions');

      // 角色管理
      Route::resource('roles', \App\Http\Controllers\Tenant\TenantRoleController::class)->names('tenant.roles');
    });
  });
});
