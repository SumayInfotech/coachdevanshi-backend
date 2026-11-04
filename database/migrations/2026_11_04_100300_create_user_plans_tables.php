<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->uuid('pathway_uuid')->nullable();
            $table->string('title');
            $table->unsignedSmallInteger('duration_months')->nullable(); // 3M / 6M
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Active', 'Expired', 'Cancelled'])->default('Active');
            $table->text('notes')->nullable();
            $table->uuid('created_by_uuid')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_renewal_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->uuid('user_plan_uuid')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['Pending', 'Contacted', 'Renewed', 'Closed'])->default('Pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::create('coach_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->uuid('coach_uuid')->nullable();
            $table->text('note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_notes');
        Schema::dropIfExists('plan_renewal_requests');
        Schema::dropIfExists('user_plans');
    }
};
