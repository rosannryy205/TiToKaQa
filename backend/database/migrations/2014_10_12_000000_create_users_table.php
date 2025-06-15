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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->string('fullname')->nullable();
            $table->integer('point')->default(500);
            $table->string('rank')->default('Thường');
            $table->integer('verify_code')->nullable();
            $table->timestamp('verify_expiry')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', ['Active', 'Block']) -> default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
