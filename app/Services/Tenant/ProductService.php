<?php

namespace App\Services\Tenant;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
  public function __construct(protected ProductRepository $productRepository) {}

  /**
   * 創建商品
   */
  public function createProduct(array $attributes): Product
  {
    try {
      DB::beginTransaction();

      $product = $this->productRepository->create([
        'name'                  => $attributes['name'],
        'description'           => $attributes['description'] ?? null,
        'image_url'             => $attributes['image_url'] ?? null,
        'inventory_management'  => $attributes['inventory_management'],
        'status'                => $attributes['status'],
      ]);

      if (isset($attributes['categories'])) {
        $product->categories()->sync($attributes['categories']);
      }

      if (isset($attributes['images'])) {
        $this->handleImageUpload($product, $attributes['images']);
      }

      if (isset($attributes['variants'])) {
        $this->handleVariants($product, $attributes['variants']);
      }

      DB::commit();

      return $product;
    } catch (\Throwable $e) {
      DB::rollBack();

      throw $e;
    }
  }

  /**
   * 更新商品
   */
  public function updateProduct($id, array $attributes): Product
  {
    try {
      DB::beginTransaction();

      $product = $this->productRepository->findOrFail($id);

      $updateData = [
        'name'                  => $attributes['name'],
        'description'           => $attributes['description'] ?? null,
        'image_url'             => $attributes['image_url'] ?? null,
        'inventory_management'  => $attributes['inventory_management'],
        'status'                => $attributes['status'],
      ];

      $product->update($updateData);

      if (isset($attributes['categories'])) {
        $product->categories()->sync($attributes['categories']);
      }

      if (isset($attributes['images'])) {
        $this->handleImageUpload($product, $attributes['images']);
      }

      // if (isset($attributes['variants'])) {
      //   $this->handleVariants($product, $attributes['variants']);
      // }

      DB::commit();

      return $product;
    } catch (\Throwable $e) {
      DB::rollBack();

      throw $e;
    }
  }

  /**
   * 刪除商品
   */
  public function deleteProduct($id): void
  {
    try {
      DB::beginTransaction();

      $product = $this->productRepository->findOrFail($id);

      $product->delete();

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();

      throw $e;
    }
  }

  /**
   * 處理圖片上傳
   */
  private function handleImageUpload(Product $product, array $images): void
  {
    $tenantId = tenant('id');

    foreach ($images as $originalImageData) {
      // 解析 base64 資料
      $parsedImageData = $this->parseBase64Image($originalImageData['data']);
      $extension = $this->getExtensionFromMimeType($parsedImageData['mime_type']);

      // 產生唯一檔名
      $filename = Str::uuid() . '.' . $extension;

      // 儲存路徑
      $storagePath = "tenants/{$tenantId}/products/{$filename}";
      $dbPath = "products/{$filename}";

      // 儲存到 public disk
      Storage::disk('public')->put($storagePath, $parsedImageData['data']);

      // 建立圖片記錄
      ProductImage::create([
        'product_id'  => $product->id,
        'filename'    => $filename,
        'path'        => $dbPath,
        'url'         => $dbPath,
        'size'        => $originalImageData['size'],
      ]);
    }

    $firstImage = ProductImage::where('product_id', $product->id)
      ->orderBy('sort')
      ->first();

    if ($firstImage) {
      $product->update([
        'image_url' => $firstImage->url
      ]);
    }
  }

  /**
   * 解析 base64 圖片資料
   */
  private function parseBase64Image(string $base64Data): array
  {
    // 檢查 base64 格式
    if (preg_match('/^data:([^;]+);base64,(.+)$/', $base64Data, $matches)) {
      $mimeType = $matches[1];
      $data = base64_decode($matches[2]);

      return [
        'mime_type' => $mimeType,
        'data'      => $data
      ];
    }

    throw new \InvalidArgumentException('Invalid base64 image format');
  }

  /**
   * 從 MIME type 取得副檔名
   */
  private function getExtensionFromMimeType(string $mimeType): string
  {
    $extensions = [
      'image/jpeg'  => 'jpg',
      'image/png'   => 'png',
      'image/gif'   => 'gif',
      'image/webp'  => 'webp',
    ];

    return $extensions[$mimeType] ?? 'jpg';
  }

  /**
   * 處理商品規格
   */
  private function handleVariants(Product $product, array $variants): void
  {
    if (!empty($variants)) {
      $product->variants()->delete();

      foreach ($variants as $variant) {
        $product->variants()->create([
          'name'              => $variant['combination'] ?? null,
          'sku'               => $variant['sku'] ?? null,
          'barcode'           => $variant['barcode'] ?? null,

          'price'             => $variant['price'] ?? 0,
          'compare_at_price'  => $variant['compare_at_price'] ?? null,
          'cost_price'        => $variant['cost_price'] ?? null,
        ]);
      }
    }
  }
}
