<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_onboarding_chapters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->unsignedTinyInteger('chapter'); // 1 - 6
            $table->json('answers')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'chapter']);
        });

        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->unique();
            $table->boolean('info_accurate')->default(false);
            $table->boolean('not_medical_advice')->default(false);
            $table->boolean('data_usage')->default(false);
            $table->boolean('communication')->default(false);
            $table->boolean('marketing')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('user_onboarding_chapters');
    }
};
