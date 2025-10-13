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
    Schema::create('line_users', function (Blueprint $table) {
      $table->id();
      $table->string('line_user_id')->unique();
      $table->foreignId('member_id')->nullable()->constrained('members');
      $table->json('profile')->nullable();
      $table->timestamp('last_event_at')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('line_users');
  }
};
