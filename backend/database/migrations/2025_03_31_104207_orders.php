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
        Schema::create('orders', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->onDelete('cascade');
            $table->timestamp('order_time')->default(DB::raw('CURRENT_TIMESTAMP')); // tự động điền thời gian khi tạo bản ghi
            $table->enum('order_status', ['Chờ xác nhận', 'Đã xác nhận', 'Đang xử lý', 'Đang giao hàng', 'Giao thành công', 'Giao thất bại', 'Đã hủy'])->default('Chờ xác nhận');
            $table->decimal('total_price', 10, 2);
            $table->string('comment')->nullable();
            $table->timestamp('review_time')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('rating');
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_address')->nullable();
            $table->string('note')->nullable();
            $table->decimal('deposit_amount', 10, 2);
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('reservations_time')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('expiration_time')->nullable();
            $table->enum('reservation_status', ['Chờ Xác Nhận', 'Đã Xác Nhận', 'Khách Đã Đến', 'Hoàn Thành', 'Hết Hạn', 'Đã hủy'])->default('Chờ Xác Nhận');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
