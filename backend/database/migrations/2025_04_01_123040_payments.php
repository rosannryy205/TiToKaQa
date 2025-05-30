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
        Schema::create('payments', function (Blueprint $table){
            $table->id();
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_method', ['Thanh toán COD', 'Thanh toán VNPAY', 'Thanh toán MOMO']);
            $table->enum('payment_status', ['Chưa thanh toán', 'Đã thanh toán']);
            $table->timestamp('payment_time')->nullable();
            $table->enum('payment_type', ['Thanh toán tiền cọc', 'Thanh toán toàn bộ']);
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->timestamps();
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
