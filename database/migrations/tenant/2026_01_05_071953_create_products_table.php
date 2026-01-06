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
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->longText('description')->nullable()->comment('商品描述');
      $table->text('image_url')->nullable()->comment('商品圖片');
      $table->string('inventory_management')->nullable()->comment('庫存管理方式');
      $table->integer('sort')->default(0)->comment('排序');
      $table->string('status')->nullable()->comment('狀態');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
