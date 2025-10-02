<?php

namespace App\Services\Tenant;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class categoryService
{
    /**
     * 創建分類
     */
    public function createCategory(array $attributes): Category
    {
      try {
          DB::beginTransaction();

          $category = Category::create([
            'name'      => $attributes['name'],
            'status'    => $attributes['status'],
            'parent_id' => $attributes['parent_id'] ?? null,
          ]);

          DB::commit();

          return $category;
      } catch (\Throwable $e) {
          DB::rollBack();

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

            $category = Category::findOrFail($id);

            $updateData = [
                'name'      => $attributes['name'],
                'status'    => $attributes['status'],
                'parent_id' => $attributes['parent_id'] ?? null,
            ];

            $category->update($updateData);

            DB::commit();

            return $category;
        } catch (\Throwable $e) {
            DB::rollBack();

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

            $category = Category::findOrFail($id);

            $category->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * 更新分類排序
     */
    public function updateCategorySort(array $categories): void
    {
        try {
            DB::beginTransaction();

            foreach ($categories as $categoryData) {
                Category::where('id', $categoryData['id'])
                    ->update([
                        'sort' => $categoryData['sort'],
                        'parent_id' => $categoryData['parent_id'] ?? null,
                    ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
