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
        Schema::create('order_toppings', function (Blueprint $table){
            $table->id();
            $table->foreignId('food_toppings_id')->constrained('food_toppings')->onDelete('cascade');
            $table->foreignId('order_detail_id')->constrained('order_details')->onDelete('cascade');
            $table->decimal('price', 8,2);
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
