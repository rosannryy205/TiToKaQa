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
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('shipper_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->nullOnDelete();
            $table->foreignId('discount_user_id')->nullable()
            ->constrained('discount_user')->nullOnDelete();
            $table->timestamp('order_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('order_status', ['Chờ xác nhận', 'Đã xác nhận', 'Đang xử lý', 'Bắt đầu giao', 'Đang giao hàng', 'Giao thành công', 'Giao thất bại', 'Đã hủy', 'Khách đã đến', 'Hoàn thành'])->default('Chờ xác nhận');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('money_reduce', 10, 2)->nullable();
            $table->string('type_order')->nullable();
            $table->unsignedInteger('tpoint_used')->nullable();
            $table->unsignedInteger('ship_cost')->nullable();
            $table->unsignedInteger('table_fee')->default(0)->comment('Phí giữ bàn (VNĐ)');
            $table->boolean('points_awarded')->default(false);
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_address')->nullable();
            $table->integer('guest_count')->nullable();
            $table->string('order_code')->unique()->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->string('canceled_reason')->nullable();
            $table->string('note')->nullable();
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
