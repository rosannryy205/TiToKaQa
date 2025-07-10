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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('shipper_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->onDelete('cascade');
            $table->timestamp('order_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('order_status', ['Chờ xác nhận', 'Đã xác nhận', 'Đang xử lý', 'Bắt đầu giao', 'Đang giao hàng', 'Giao thành công', 'Giao thất bại', 'Đã hủy', 'Khách đã đến', 'Hoàn thành'])->default('Chờ xác nhận');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('shippingFee', 10, 2)->nullable();
            $table->decimal('money_reduce', 10, 2)->nullable();
            $table->string('comment')->nullable();
            $table->timestamp('review_time')->nullable();
            $table->boolean('points_awarded')->default(false);
            $table->integer('rating')->nullable();
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_address')->nullable();
            $table->integer('guest_count')->nullable();
            $table->string('note')->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('expiration_time')->nullable();
            $table->string('reservation_code')->nullable()->unique();
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
