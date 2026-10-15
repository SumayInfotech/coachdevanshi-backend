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
       Schema::create('user_fcm_tokens', function (Blueprint $table) {
            $table->uuid('uuid')->primary();

            $table->uuid('user_uuid');
            $table->string('fcm_token')->unique();

            $table->string('device_type')->nullable(); // android, ios, web
            $table->string('device_name')->nullable(); // samsung, oneplus

            $table->timestamps();

            $table->foreign('user_uuid')
                ->references('uuid')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_fcm_tokens');
    }
};
