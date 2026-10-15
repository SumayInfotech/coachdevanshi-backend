<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            /* ===============================
               USER ROLE
            =============================== */
            $table->enum('user_type', ['admin', 'customer'])
                  ->default('customer')
                  ->after('mobile');

            /* ===============================
               IDENTIFIER TYPE (email / mobile)
            =============================== */
            $table->enum('identifier_type', ['mobile', 'email'])
                  ->nullable()
                  ->after('user_type');

            /* ===============================
               SIGNUP FLOW
            =============================== */
            $table->enum('sign_up_type', ['otp', 'password'])
                  ->nullable()
                  ->after('identifier_type');

            $table->string('otp', 6)
                  ->nullable()
                  ->after('sign_up_type');

            $table->timestamp('otp_expires_at')
                  ->nullable()
                  ->after('otp');

            $table->boolean('is_signup')
                  ->default(false)
                  ->after('otp_expires_at');

            /* ===============================
               PROFILE COMPLETION
            =============================== */
            $table->boolean('is_profile_completed')
                  ->default(false)
                  ->after('is_signup');

            /* ===============================
               FCM TOKEN
            =============================== */
            $table->text('fcm_token')
                  ->nullable()
                  ->after('is_profile_completed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_type',
                'identifier_type',
                'sign_up_type',
                'otp',
                'otp_expires_at',
                'is_signup',
                'is_profile_completed',
                'fcm_token',
            ]);
        });
    }
};
