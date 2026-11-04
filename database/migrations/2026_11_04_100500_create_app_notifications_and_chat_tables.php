<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->string('type', 50);   // micro_checkin_reminder, weekly_checkin_reminder, plan_expiry, chat_message, ...
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('badge')->nullable();      // e.g. "Due Today", "Due This Sunday"
            $table->string('action', 50)->nullable(); // screen the app should open
            $table->json('data')->nullable();
            $table->string('dedupe_key')->nullable()->index();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index(); // the client the conversation belongs to
            $table->enum('sender_type', ['client', 'coach', 'system']);
            $table->uuid('sender_uuid')->nullable();
            $table->text('message')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('app_notifications');
    }
};
