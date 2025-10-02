<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    \View::composer('*', function ($view) {
      try {
        if (tenant()) {
          // 租戶環境 menu
          $menuPath = base_path('resources/menu/tenantverticalMenu.json');
        } else {
          // 中央環境 menu
          $menuPath = base_path('resources/menu/verticalMenu.json');
        }

        // 檢查檔案是否存在
        if (!file_exists($menuPath)) {
          throw new \Exception("Menu file not found: {$menuPath}");
        }

        $verticalMenuJson = file_get_contents($menuPath);

        if ($verticalMenuJson === false) {
          throw new \Exception("Failed to read menu file: {$menuPath}");
        }

        $verticalMenuData = json_decode($verticalMenuJson);

        if (json_last_error() !== JSON_ERROR_NONE) {
          throw new \Exception("Invalid JSON in menu file: " . json_last_error_msg());
        }

        // 確保 menuData 結構正確
        if (!$verticalMenuData || !isset($verticalMenuData->menu)) {
          throw new \Exception("Invalid menu data structure");
        }

        $view->with('menuData', [$verticalMenuData]);

      } catch (\Exception $e) {
        // 記錄錯誤並提供預設選單
        \Log::error('Menu loading error: ' . $e->getMessage());

        // 提供空的選單結構作為後備
        $fallbackMenu = (object) [
          'menu' => []
        ];

        $view->with('menuData', [$fallbackMenu]);
      }
    });
  }
}
