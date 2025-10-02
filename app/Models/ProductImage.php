<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::creating(function (Model $model) {
            if (empty($model->sort)) {
                $maxSort = static::max('sort');

                $model->sort = is_null($maxSort) ? 1 : $maxSort + 1;
            }
        });

        static::deleting(function (Model $model) {
            if (\Storage::disk('public')->exists('tenants/' . tenant('id') . '/' . $model->path)) {
                \Storage::disk('public')->delete('tenants/' . tenant('id') . '/' . $model->path);
            }
        });
    }
}
