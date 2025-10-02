<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price'             => 'integer',
        'compare_at_price'  => 'integer',
        'cost_price'        => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->belongsToMany(VariantValue::class, 'variant_value_has_variant', 'variant_id', 'variant_value_id');
    }
}
