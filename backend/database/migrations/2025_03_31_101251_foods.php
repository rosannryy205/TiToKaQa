<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image');
            $table->integer('stock')->default(100);
            $table->string('sale_price')->nullable();
            $table->string('description');
            $table->integer('quantity_sold')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('flash_sale_price', 10, 2)->nullable();
            $table->integer('flash_sale_quantity')->nullable();
            $table->integer('flash_sale_sold')->default(0);
            $table->timestamp('flash_sale_start')->nullable();
            $table->timestamp('flash_sale_end')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
