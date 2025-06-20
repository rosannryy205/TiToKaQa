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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_user_id')->nullable();
            $table->string('sender_guest_id')->nullable();

            $table->unsignedBigInteger('receiver_user_id')->nullable();
            $table->string('receiver_guest_id')->nullable();

            $table->enum('status', ['Chờ xử lý', 'Đang xử lý', 'Đã xử lý'])->default('Chờ xử lý');
            $table->timestamp('last_message_at');
            $table->boolean('is_read')->default(false);
            $table->string('sender_name');
            $table->string('sender_avatar');
            $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('receiver_user_id')->references('id')->on('users')->onDelete('set null');
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
