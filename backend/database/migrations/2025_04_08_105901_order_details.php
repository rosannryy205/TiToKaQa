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
        Schema::create('order_details', function (Blueprint $table){
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('food_id')->nullable()->constrained('foods')->onDelete('cascade');
            $table->foreignId('combo_id')->nullable()->constrained('combos')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 8,2);
            $table->enum('type', ['food', 'combo', 'topping'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
