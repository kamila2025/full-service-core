<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends Repository
{
    protected $fieldSearchable = [
        'name' => 'like',
        'status',
        'categories.id' => 'in',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Product::class;
    }

    public function getProducts(array $attributes = [])
    {
        //$this->applyCriteria();

        $this->model = $this->model->when($attributes['name'] ?? false, function ($query) use ($attributes) {
            $query->where('name', 'like', '%' . $attributes['name'] . '%');
        });

        $this->model = $this->model->when($attributes['status'] ?? false, function ($query) use ($attributes) {
            $query->where('status', $attributes['status']);
        });

        $this->model = $this->model->when($attributes['categories'] ?? false, function ($query) use ($attributes) {
            $query->whereHas('categories', function ($query) use ($attributes) {
                $query->whereIn('categories.id', $attributes['categories']);
            });
        });

        return $this->model
            ->with('categories', 'variants')
            ->latest()
            ->get();
    }
}
