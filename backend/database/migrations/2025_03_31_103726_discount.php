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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); 
            $table->string('name');         
            $table->integer('discount_value'); 
            $table->enum('discount_method', ['percent', 'fixed'])->default('fixed'); 
            $table->enum('discount_type', ['freeship', 'salefood'])->default('freeship'); 
            $table->integer('max_discount_amount')->nullable()->comment('Giá trị giảm tối đa, áp dụng cho discount_method là percent');
            $table->integer('min_order_value')->nullable()->default(0)->comment('Giá trị đơn hàng tối thiểu để được áp dụng mã giảm giá');
            $table->timestamp('start_date')->nullable(); 
            $table->timestamp('end_date')->nullable();   
            $table->enum('status', ['active', 'inactive'])->default('active'); 
            $table->integer('used')->default(0);
            $table->integer('usage_limit')->default(1);
            $table->softDeletes();                      
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
