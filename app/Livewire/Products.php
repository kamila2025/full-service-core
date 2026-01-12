<?php

namespace App\Livewire;

use App\Livewire\BaseComponent;
use App\Models\Product;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Products extends BaseComponent
{
  public $product;
  public $quantity = 1;
  public $selectedVariant = null;
  public $activeTab = 'description';

  public function mount($product_id)
  {
    $this->product = Product::with(['images', 'categories.parent', 'variants.values'])
      ->where('status', \App\Enums\Tenant\Product\ProductStatusEnum::已發佈)
      ->findOrFail($product_id);

    // 如果有變體，預設選擇第一個
    if ($this->product->variants->count() > 0) {
      $this->selectedVariant = $this->product->variants->first()->id;
    }
  }

  public function updateQuantity($change)
  {
    $this->quantity = max(1, min(10, $this->quantity + $change));
  }

  public function selectVariant($variantId)
  {
    $this->selectedVariant = $variantId;
  }

  public function switchTab($tab)
  {
    $this->activeTab = $tab;
  }

  public function addToCart()
  {
    // TODO: 實作加入購物車邏輯
    $this->dispatch('notify', [
      'status' => 'success',
      'message' => "已加入 {$this->quantity} 件商品至購物車",
    ]);
  }

  public function getCurrentPriceProperty()
  {
    if ($this->selectedVariant) {
      $variant = $this->product->variants->find($this->selectedVariant);
      return $variant ? $variant->price : $this->product->variants->min('price') ?? 0;
    }

    return $this->product->variants->min('price') ?? 0;
  }

  public function getOriginalPriceProperty()
  {
    if ($this->selectedVariant) {
      $variant = $this->product->variants->find($this->selectedVariant);
      return $variant && $variant->compare_at_price ? $variant->compare_at_price : null;
    }

    return null;
  }

  public function getBreadcrumbProperty()
  {
    $breadcrumbs = [];
    $firstCategory = $this->product->categories->first();

    if ($firstCategory) {
      if ($firstCategory->parent_id) {
        $parentCategory = $firstCategory->parent;
        if ($parentCategory) {
          $breadcrumbs[] = $parentCategory;
        }
      }
      $breadcrumbs[] = $firstCategory;
    }

    $breadcrumbs[] = $this->product->name;

    return $breadcrumbs;
  }

  public function getRelatedProductsProperty()
  {
    $categoryIds = $this->product->categories->pluck('id');

    return Product::with(['images', 'variants'])
      ->where('status', \App\Enums\Tenant\Product\ProductStatusEnum::已發佈)
      ->where('id', '!=', $this->product->id)
      ->whereHas('categories', function ($q) use ($categoryIds) {
        $q->whereIn('categories.id', $categoryIds);
      })
      ->limit(4)
      ->get();
  }

  public function render()
  {
    return view('livewire.products', [
      'product' => $this->product,
      'currentPrice' => $this->currentPrice,
      'originalPrice' => $this->originalPrice,
      'breadcrumb' => $this->breadcrumb,
      'relatedProducts' => $this->relatedProducts,
    ]);
  }
}
