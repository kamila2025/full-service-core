<?php

namespace App\Livewire;

use App\Livewire\BaseComponent;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Categories extends BaseComponent
{
  use WithPagination;

  public $categories;
  public $categoryID = null;
  public $perPage = 12;

  public function mount($category_id = null)
  {
    // 載入所有已發佈的分類（包含父子關係）
    $this->categories = Category::where('status', \App\Enums\Tenant\Category\CategoryStatusEnum::已發佈)
      ->whereNull('parent_id')
      ->orderBy('sort', 'asc')
      ->with('children')
      ->get();

    // 如果有 category_id，設置分類 ID
    if ($category_id) {
      // 嘗試作為 ID 查找
      $category = Category::find($category_id);
      // 如果找不到，嘗試作為名稱查找
      if (!$category) {
        $category = Category::where('name', $category_id)->first();
      }
      if ($category) {
        $this->categoryID = $category->id;
      }
    }
  }

  public function selectCategory(?int $categoryId = null)
  {
    $this->categoryID = $categoryId;
    $this->resetPage();
  }

  public function resetFilters()
  {
    $this->categoryID = null;
    $this->resetPage();
  }

  public function getProductsProperty()
  {
    $query = Product::query()
      ->with(['categories', 'images', 'variants'])
      ->where('status', \App\Enums\Tenant\Product\ProductStatusEnum::已發佈);

    if ($this->categoryID) {
      // 如果選擇了分類，顯示該分類及其所有子分類的商品
      $categoryIds = Category::where('id', $this->categoryID)
        ->orWhere('parent_id', $this->categoryID)
        ->pluck('id');

      $query->whereHas('categories', function ($q) use ($categoryIds) {
        $q->whereIn('categories.id', $categoryIds);
      });
    }

    return $query->orderBy('sort')->orderBy('created_at', 'desc')->paginate($this->perPage);
  }

  public function getBreadcrumbProperty()
  {
    if ($this->categoryID) {
      $category = Category::find($this->categoryID);
      if ($category) {
        // 如果有父分類，顯示父分類 / 子分類
        if ($category->parent_id) {
          $parentCategory = Category::find($category->parent_id);
          return $parentCategory?->name . ' / ' . $category->name;
        }

        return $category->name;
      }
    }

    return '全部商品';
  }

  public function isCategoryActive($category)
  {
    // 如果當前選擇的分類就是這個分類
    if ($this->categoryID == $category->id) {
      return true;
    }

    // 如果當前選擇的分類是這個分類的子分類
    if ($this->categoryID && $category->children) {
      foreach ($category->children as $child) {
        if ($child->id == $this->categoryID) {
          return true;
        }
      }
    }

    return false;
  }

  public function render()
  {
    return view('livewire.categories', [
      'categories'  => $this->categories,
      'products'    => $this->products,
      'breadcrumb'  => $this->breadcrumb,
    ]);
  }
}
