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
        Schema::create('food_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('spin_id')->nullable()->constrained('lucky_wheel_spins')->nullOnDelete();
            $table->json('food_snapshot')->nullable();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('food_id');
            $table->timestamp('expired_at');
            $table->timestamps();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
            $table->unique(['spin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_rewards');
    }
};
