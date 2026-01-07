<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $guarded = [];

  protected $casts = [
    'status' => \App\Enums\Tenant\Category\CategoryStatusEnum::class,
  ];

  public function products()
  {
    return $this->belongsToMany(Product::class, 'category_has_product');
  }

  public function parent()
  {
    return $this->belongsTo(Category::class, 'parent_id');
  }

  public function children()
  {
    return $this->hasMany(Category::class, 'parent_id')->orderBy('sort');
  }

  protected static function booted()
  {
    static::creating(function ($model) {
      if (empty($model->sort)) {
        $maxSort = static::max('sort');

        $model->sort = is_null($maxSort) ? 1 : $maxSort + 1;
      }
    });
  }
}
