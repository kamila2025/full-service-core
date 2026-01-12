<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends Repository
{
  /**
   * Specify Model class name
   *
   * @return string
   */
  public function model(): string
  {
    return Category::class;
  }

  public function getCategories(array $attributes = [])
  {
    return $this->model
      ->whereNotNull('categories.parent_id')
      ->with('products', 'parent', 'children')
      ->join('categories as parent_categories', 'categories.parent_id', '=', 'parent_categories.id')
      ->select('categories.*')
      ->orderBy('parent_categories.sort', 'asc')
      ->orderBy('categories.sort', 'asc')
      ->get();
  }
}
