<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('options')->nullable()->comment('商品選項');
            $table->string('name')->nullable()->comment('商品名稱');
            $table->string('sku')->unique()->nullable()->comment('編號');
            $table->string('barcode')->unique()->nullable()->comment('條碼');
            $table->decimal('width', 10, 2)->default(0)->comment('寬度');
            $table->decimal('height', 10, 2)->default(0)->comment('高度');
            $table->decimal('length', 10, 2)->default(0)->comment('長度');
            $table->decimal('weight', 10, 2)->default(0)->comment('重量');
            $table->string('weight_unit')->default('kg')->comment('重量單位');
            $table->decimal('grams', 10, 2)->default(0)->comment('克重');
            $table->decimal('price', 10, 2)->comment('價格');
            $table->decimal('compare_at_price', 10, 2)->nullable()->comment('比較價格');
            $table->decimal('cost_price', 10, 2)->nullable()->comment('成本價格');
            $table->boolean('taxable')->default(false)->comment('是否稅金');
            $table->boolean('shipping')->default(false)->comment('是否免運費');
            $table->integer('sort')->default(0);
            $table->boolean('inventory_policy')->default(false)->comment('表示當庫存為零時是否繼續銷售產品');
            $table->string('inventory_management')->nullable()->comment('庫存管理方式');
            $table->string('status')->nullable()->comment('狀態');
            $table->timestamps();
        });

        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'name']);
        });

        Schema::create('variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_type_id')->constrained('variant_types')->onDelete('cascade');
            $table->string('name');
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->unique(['variant_type_id', 'name']);
        });

        Schema::create('variant_value_has_variant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_value_id')->constrained('variant_values')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('variants')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_value_has_variant');
        Schema::dropIfExists('variant_values');
        Schema::dropIfExists('variant_types');
        Schema::dropIfExists('variants');
    }
};
