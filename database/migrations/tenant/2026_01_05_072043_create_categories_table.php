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
    Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
      $table->string('name');
      $table->integer('sort')->default(0)->comment('排序');
      $table->string('status')->nullable()->comment('狀態');
      $table->timestamps();
    });

    Schema::create('category_has_product', function (Blueprint $table) {
      $table->id();
      $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
      $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('category_has_product');
    Schema::dropIfExists('categories');
  }
};
