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
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('line_users', function (Blueprint $table) {
            $table->id();
            $table->string('line_user_id')->unique();
            $table->foreignId('member_id')->nullable()->constrained();
            $table->json('profile')->nullable();
            $table->timestamp('last_event_at')->nullable();
            $table->timestamps();
        });

        Schema::create('keyword_rules', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->text('reply_template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->json('target')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
        Schema::dropIfExists('keyword_rules');
        Schema::dropIfExists('line_users');
        Schema::dropIfExists('members');
    }
};
