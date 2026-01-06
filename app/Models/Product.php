<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $guarded = [];

  protected $casts = [
    'inventory_management'  => \App\Enums\Tenant\Product\ProductInventoryManagementEnum::class,
    'status'                => \App\Enums\Tenant\Product\ProductStatusEnum::class,
  ];

  public function images()
  {
    return $this->hasMany(ProductImage::class)->orderBy('sort');
  }

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'category_has_product');
  }

  public function variants()
  {
    return $this->hasMany(Variant::class)->orderBy('sort');
  }

  protected static function booted()
  {
    static::creating(function (Model $model) {
      if (empty($model->sort)) {
        $maxSort = static::max('sort');

        $model->sort = is_null($maxSort) ? 1 : $maxSort + 1;
      }
    });
  }
}
