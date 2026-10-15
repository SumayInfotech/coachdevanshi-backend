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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_notification')->default(false)->after('is_profile_completed');
            $table->boolean('sms_notification')->default(false)->after('email_notification');
            $table->boolean('whatsapp_notification')->default(false)->after('sms_notification');
            $table->enum('language', ['en', 'it'])->default('en')->after('whatsapp_notification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'email_notification',
                'sms_notification',
                'whatsapp_notification',
                'language'
            ]);

        });
    }
};