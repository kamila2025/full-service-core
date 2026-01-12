<?php

namespace App\Services\Tenant;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService
{
  public function __construct(protected CategoryRepository $categoryRepository) {}

  /**
   * 創建分類
   */
  public function createCategory(array $attributes): Category
  {
    try {
      DB::beginTransaction();

      $category = $this->categoryRepository->create($attributes);

      DB::commit();

      return $category;
    } catch (\Throwable $e) {
      DB::rollBack();

      Log::error('創建分類失敗', [
        'message'     => $e->getMessage(),
        'attributes'  => $attributes,
        'tenant_id'   => tenant('id'),
      ]);

      throw $e;
    }
  }

  /**
   * 更新分類
   */
  public function updateCategory($id, array $attributes): Category
  {
    try {
      DB::beginTransaction();

      $this->categoryRepository->update($attributes, $id);

      DB::commit();

      return $this->categoryRepository->findOrFail($id);
    } catch (\Throwable $e) {
      DB::rollBack();

      Log::error('更新分類失敗', [
        'message'     => $e->getMessage(),
        'category_id' => $id,
        'attributes'  => $attributes,
        'tenant_id'   => tenant('id'),
      ]);

      throw $e;
    }
  }

  /**
   * 刪除分類
   */
  public function deleteCategory($id): void
  {
    try {
      DB::beginTransaction();

      $this->categoryRepository->delete($id);

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();

      Log::error('刪除分類失敗', [
        'message'     => $e->getMessage(),
        'category_id' => $id,
        'tenant_id'   => tenant('id'),
      ]);

      throw $e;
    }
  }

  /**
   * 根據 ID 獲取分類
   */
  public function getCategoryById(int $id): Category
  {
    return $this->categoryRepository->findOrFail($id);
  }

  /**
   * 獲取分類列表
   */
  public function getCategoryTree(): array
  {
    $categories = $this->categoryRepository->all();

    $categories = $categories->map(function ($category) {
      return [
        'id'            => $category->id,
        'name'          => $category->name,
        'parent_id'     => $category->parent_id,
        'sort'          => $category->sort,
        'status_name'   => $category->status->name,
        'status_badge'  => $category->status->badgeClass(),
        'product_count' => $category->products->count(),
      ];
    });

    return $categories->toArray();
  }

  /**
   * 更新分類排序
   */
  public function updateCategorySort(array $categories): void
  {
    try {
      DB::beginTransaction();

      foreach ($categories as $categoryData) {
        $category = $this->categoryRepository->findOrFail($categoryData['id']);

        $this->categoryRepository->update([
          'sort'      => $categoryData['sort'],
          'parent_id' => $categoryData['parent_id'] ?? null,
        ], $category->id);
      }

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();

      Log::error('更新分類排序失敗', [
        'message'     => $e->getMessage(),
        'categories'  => $categories,
        'tenant_id'   => tenant('id'),
      ]);

      throw $e;
    }
  }
}
