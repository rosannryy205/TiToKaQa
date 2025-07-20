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
        DB::statement('SET @@sql_mode = REPLACE(@@sql_mode, "NO_ZERO_DATE", "");');
            Schema::create('flash_sale_foods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('food_id')->constrained('foods')->onDelete('cascade');
                $table->decimal('original_price', 10, 2);
                $table->decimal('sale_price', 10, 2);
                $table->unsignedInteger('quantity_limit')->nullable();
                $table->dateTime('start_time');
                $table->dateTime('end_time');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_foods');
    }
};
