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
    Schema::create('members', function (Blueprint $table) {
      $table->id();
      $table->string('name')->nullable();
      $table->string('phone')->nullable()->index();
      $table->string('email')->nullable()->index();
      $table->date('birthday')->nullable()->comment('生日');
      $table->string('gender')->nullable()->comment('性別');
      $table->string('city')->nullable()->comment('城市');
      $table->string('district')->nullable()->comment('地區');
      $table->string('zipcode')->nullable()->comment('郵遞區號');
      $table->string('address')->nullable()->comment('地址');
      $table->string('status')->nullable()->comment('狀態');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('members');
  }
};
