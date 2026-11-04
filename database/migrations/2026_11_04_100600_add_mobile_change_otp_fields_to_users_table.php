<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns used by ProfileController send-mobile-otp / verify-mobile-otp (change mobile number with OTP).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pending_phone_code', 5)->nullable()->after('mobile');
            $table->string('pending_mobile')->nullable()->after('pending_phone_code');
            $table->string('mobile_otp', 6)->nullable()->after('pending_mobile');
            $table->timestamp('mobile_otp_expires_at')->nullable()->after('mobile_otp');
            $table->boolean('is_mobile_verified')->default(false)->after('mobile_otp_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pending_phone_code', 'pending_mobile', 'mobile_otp', 'mobile_otp_expires_at', 'is_mobile_verified']);
        });
    }
};
