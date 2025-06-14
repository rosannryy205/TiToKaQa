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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('vnpay_txn_ref')->nullable()->comment('Mã tham chiếu giao dịch duy nhất của VNPAY');
            $table->string('transaction_id')->nullable()->comment('ID giao dịch của VNPAY hoặc MoMo');
            $table->string('bank_code')->nullable()->comment('Mã ngân hàng được sử dụng để thanh toán (VNPAY)');
            $table->string('card_type')->nullable()->comment('Loại thẻ được sử dụng để thanh toán (VNPAY)');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_method', ['COD', 'VNPAY', 'MOMO']);
            $table->enum('payment_status', ['Đang chờ xử lý', 'Đã thanh toán', 'Thất bại', 'Đã hoàn tiền']);
            $table->timestamp('payment_time')->nullable();
            $table->enum('payment_type', ['Tiền cọc', 'Thanh toán toàn bộ']);
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
