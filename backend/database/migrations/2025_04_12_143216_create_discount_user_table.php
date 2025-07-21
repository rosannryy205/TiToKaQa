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
        Schema::create('discount_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->integer('point_used')->default(0);
            $table->dateTime('exchanged_at');
            $table->dateTime('expiry_at');   
            $table->dateTime('used_at')->nullable(); 
            $table->string('source')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'discount_id']);
        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_user');
    }
};
