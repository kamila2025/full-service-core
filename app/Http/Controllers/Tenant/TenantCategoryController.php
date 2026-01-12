<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Tenant\PermissionNameEnum;
use Illuminate\Http\Request;
use App\Services\Tenant\CategoryService;

class TenantCategoryController extends BaseTenantController
{
  public function __construct(protected CategoryService $categoryService) {}

  /**
   * 分類管理頁面
   */
  public function index()
  {
    $this->authorizePermission(PermissionNameEnum::分類管理);

    return view('content.tenant.product.tenant-category');
  }

  /**
   * 創建分類
   */
  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::分類管理);

    try {
      $attributes = $request->validate([
        'name'      => 'required|string|max:255',
        'status'    => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
      ], [], [
        'name'      => '分類名稱',
        'status'    => '狀態',
        'parent_id' => '主分類',
      ]);

      $category = $this->categoryService->createCategory($attributes);

      return $this->successResponse('分類新增成功', ['category' => $category], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('分類新增失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 顯示分類資料
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNameEnum::分類管理);

    $category = $this->categoryService->getCategoryById($id);

    return $this->successResponse('分類取得成功', ['category' => $category], 200);
  }

  /**
   * 更新分類
   */
  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNameEnum::分類管理);

    try {
      $attributes = $request->validate([
        'name'      => 'required|string|max:255',
        'status'    => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
      ], [], [
        'name'      => '分類名稱',
        'status'    => '狀態',
        'parent_id' => '主分類',
      ]);

      $category = $this->categoryService->updateCategory($id, $attributes);

      return $this->successResponse('分類更新成功', null, 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('分類更新失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 刪除分類
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNameEnum::分類管理);

    try {
      $this->categoryService->deleteCategory($id);

      return $this->successResponse('分類刪除成功', null, 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('分類刪除失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 取得分類樹
   */
  public function getCategoryTree()
  {
    $this->authorizePermission(PermissionNameEnum::分類管理);

    $categories = $this->categoryService->getCategoryTree();

    return $this->successResponse('分類取得成功', $categories, 200);
  }

  /**
   * 更新分類排序
   */
  public function updateSort(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::分類管理);

    try {
      $request->validate([
        'categories'              => 'required|array',
        'categories.*.id'         => 'required|exists:categories,id',
        'categories.*.sort'       => 'required|integer|min:0',
        'categories.*.parent_id'  => 'nullable|exists:categories,id',
      ], [], [
        'categories'              => '分類資料',
        'categories.*.id'         => '分類ID',
        'categories.*.sort'       => '排序',
        'categories.*.parent_id'  => '父分類',
      ]);

      $this->categoryService->updateCategorySort($request->categories);

      return $this->successResponse('排序更新成功', null, 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('排序更新失敗，請聯絡管理者', 500);
    }
  }
}
