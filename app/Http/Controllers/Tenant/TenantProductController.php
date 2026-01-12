<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Tenant\PermissionNameEnum;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\ProductRepository;
use App\Services\Tenant\ProductService;
use App\Services\Tenant\CategoryService;
use Yajra\DataTables\Facades\DataTables;

class TenantProductController extends BaseTenantController
{
  public function __construct(protected ProductService $productService, protected ProductRepository $productRepository, protected CategoryService $categoryService) {}

  /**
   * 分類管理頁面
   */
  public function index(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    if ($request->ajax()) {
      $attributes = $request->validate([
        'name'          => 'nullable|string|max:255',
        'status'        => 'nullable|string|max:255',
        'categories'    => 'nullable|array',
        'categories.*'  => 'integer|exists:categories,id',
      ]);

      $records = $this->productRepository->getProducts($attributes);

      return DataTables::of($records)
        ->addColumn('name',                   fn($record) => $record->name)
        ->addColumn('image_url',              fn($record) => $record->image_url ? asset('storage/tenants/' . tenant('id') . '/' . $record->image_url) : null)
        ->addColumn('categories',             fn($record) => $record->categories->pluck('name')->toArray())
        ->addColumn('inventory_management',   fn($record) => 0)
        ->addColumn('status_name',            fn($record) => $record->status->name)
        ->addColumn('status_badge',           fn($record) => $record->status->badgeClass())
        ->addColumn('price',                  fn($record) => $record->variants->first()->price ?? 0)
        ->make(true);
    }

    return view('content.tenant.product.tenant-product', [
      'categories' => $this->categoryService->getCategories(),
    ]);
  }

  /**
   * 顯示新增商品表單
   */
  public function create()
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    return view('content.tenant.product.tenant-product-add', [
      'categories' => $this->categoryService->getCategories(),
    ]);
  }

  /**
   * 創建商品
   */
  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    try {
      $attributes = $request->validate([
        'name'                        => 'required|string|max:255',
        'description'                 => 'nullable|string',
        'image_url'                   => 'nullable|string|max:255',
        'inventory_management'        => 'nullable|string|max:255',
        'status'                      => 'required|string|max:255',
        // 分類
        'categories'                  => 'nullable|array',
        // 圖片
        'images'                      => 'nullable|array',
        'images.*.name'               => 'required|string|max:255',
        'images.*.type'               => 'required|string|in:image/jpeg,image/png,image/jpg,image/gif',
        'images.*.size'               => 'required|integer|max:10485760',
        'images.*.data'               => 'required|string',
        // 多規格
        'variants'                    => 'required|array',
        'variants.*.price'            => 'required|numeric',
        'variants.*.compare_at_price' => 'nullable|numeric',
        'variants.*.cost_price'       => 'nullable|numeric',
      ], [], [
        'name'                        => '商品名稱',
        'description'                 => '商品描述',
        'image_url'                   => '商品圖片',
        'inventory_management'        => '庫存管理方式',
        'status'                      => '狀態',
        // 分類
        'categories'                  => '商品分類',
        'categories.*'                => '商品分類',
        // 圖片
        'images'                      => '商品圖片',
        'images.*.name'               => '圖片檔名',
        'images.*.type'               => '圖片格式',
        'images.*.size'               => '圖片大小',
        'images.*.data'               => '圖片資料',
        // 多規格
        'variants'                    => '商品規格',
        'variants.*.price'            => '售價',
        'variants.*.compare_at_price' => '原價',
        'variants.*.cost_price'       => '成本價',
      ]);

      $product = $this->productService->createProduct($attributes);

      return $this->successResponse('商品新增成功', ['redirect_url' => route('tenant.products.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('商品新增失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 顯示商品資料
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    $product = $this->productRepository->findOrFail($id);

    return $this->successResponse('商品取得成功', ['product' => $product], 200);
  }

  /**
   * 顯示編輯商品表單
   */
  public function edit($id)
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    $product = Product::with('categories', 'variants')->findOrFail($id);

    return view('content.tenant.product.tenant-product-add', [
      'product'     => $product,
      'categories'  => $this->categoryService->getCategories(),
    ]);
  }

  /**
   * 更新商品
   */
  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    try {
      $attributes = $request->validate([
        'name'                        => 'required|string|max:255',
        'description'                 => 'nullable|string',
        'image_url'                   => 'nullable|string|max:255',
        'status'                      => 'required|string|max:255',
        'inventory_management'        => 'nullable|string|max:255',
        // 分類
        'categories'                  => 'nullable|array',
        // 圖片
        'images'                      => 'nullable|array',
        'images.*.name'               => 'required|string|max:255',
        'images.*.type'               => 'required|string|in:image/jpeg,image/png,image/jpg,image/gif',
        'images.*.size'               => 'required|integer|max:10485760',
        'images.*.data'               => 'required|string',
        // 多規格
        'variants'                    => 'required|array',
        'variants.*.price'            => 'required|numeric',
        'variants.*.compare_at_price' => 'nullable|numeric',
        'variants.*.cost_price'       => 'nullable|numeric',
      ], [], [
        'name'                        => '商品名稱',
        'description'                 => '商品描述',
        'image_url'                   => '商品圖片',
        'status'                      => '狀態',
        'inventory_management'        => '庫存管理方式',
        // 分類
        'categories'                  => '商品分類',
        // 圖片
        'images'                      => '商品圖片',
        'images.*.name'               => '圖片檔名',
        'images.*.type'               => '圖片格式',
        'images.*.size'               => '圖片大小',
        'images.*.data'               => '圖片資料',
        // 多規格
        'variants'                    => '商品規格',
        'variants.*.price'            => '售價',
        'variants.*.compare_at_price' => '原價',
        'variants.*.cost_price'       => '成本價',
      ]);

      $product = $this->productService->updateProduct($id, $attributes);

      return $this->successResponse('商品更新成功', ['redirect_url' => route('tenant.products.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('商品更新失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 刪除商品
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    try {
      $this->productService->deleteProduct($id);

      return $this->successResponse('商品刪除成功', ['redirect_url' => route('tenant.products.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('商品刪除失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 刪除商品圖片
   */
  public function destroyImage($id)
  {
    $this->authorizePermission(PermissionNameEnum::商品管理);

    try {
      $image = ProductImage::findOrFail($id);

      $product = $image->product;

      $image->delete();

      $product->load('images');

      // 檢查是否還有其他圖片
      if ($product->images->count() > 0) {
        $firstImage = $product->images->first();

        $product->update(['image_url' => $firstImage->url]);
      } else {
        $product->update(['image_url' => null]);
      }

      return $this->successResponse('圖片刪除成功', null, 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('圖片刪除失敗：' . $e->getMessage(), 500);
    }
  }
}
