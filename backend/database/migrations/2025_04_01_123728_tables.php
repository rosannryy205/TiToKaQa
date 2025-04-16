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
        Schema::create('tables', function (Blueprint $table){
            $table->id();
            $table->integer('capacity');
            $table->integer('table_number');
            $table->enum('status', ['Bàn trống', 'Có khách', 'Đã đặt trước'])->default('Bàn trống');
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
