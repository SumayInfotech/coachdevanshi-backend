<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->after('last_name');
            $table->date('dob')->nullable()->after('nickname');
            $table->string('gender', 30)->nullable()->after('dob');
            $table->string('city_name')->nullable()->after('city_id');
            $table->string('country_name')->nullable()->after('city_name');
            $table->string('timezone', 64)->nullable()->after('country_name');
            $table->string('weekly_checkin_day', 10)->default('sunday')->after('language');
            $table->boolean('push_notification')->default(true)->after('weekly_checkin_day');
            $table->string('health_source', 30)->nullable()->after('push_notification'); // apple_health, google_fit
            $table->boolean('is_health_connected')->default(false)->after('health_source');
            $table->boolean('is_home_tour_completed')->default(false)->after('is_health_connected');
            $table->boolean('is_onboarding_completed')->default(false)->after('is_home_tour_completed');
            $table->timestamp('onboarding_completed_at')->nullable()->after('is_onboarding_completed');
            $table->uuid('pathway_uuid')->nullable()->after('onboarding_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nickname', 'dob', 'gender', 'city_name', 'country_name', 'timezone',
                'weekly_checkin_day', 'push_notification', 'health_source', 'is_health_connected',
                'is_home_tour_completed', 'is_onboarding_completed', 'onboarding_completed_at', 'pathway_uuid',
            ]);
        });
    }
};
