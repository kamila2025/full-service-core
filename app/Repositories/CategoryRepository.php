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
}
