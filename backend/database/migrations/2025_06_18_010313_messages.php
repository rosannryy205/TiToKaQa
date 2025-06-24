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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id')->nullable();

            $table->unsignedBigInteger('sender_user_id')->nullable();
            $table->string('sender_guest_id')->nullable();

            $table->unsignedBigInteger('receiver_user_id')->nullable();
            $table->string('receiver_guest_id')->nullable();
            $table->text('message');
            $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('receiver_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
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
