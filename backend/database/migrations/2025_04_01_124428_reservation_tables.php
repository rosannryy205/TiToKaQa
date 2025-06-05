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
        Schema::create('reservation_tables', function (Blueprint $table){
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            // $table->timestamp('assigned_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('reserved_from')->nullable();
            $table->timestamp('reserved_to')->nullable();
            // $table->enum('reservation_status', ['Chờ Xác Nhận', 'Đã xác nhận', 'Khách Đã Đến', 'Hoàn Thành', 'Đã Hủy'])->default('Chờ Xác Nhận');

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
