<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daily wellness log / micro check-in
        Schema::create('daily_checkins', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->date('record_date');
            $table->unsignedTinyInteger('baseline_day')->nullable(); // D1 - D7 of the first-week baseline
            $table->decimal('weight', 6, 2)->nullable();
            $table->enum('weight_unit', ['kg', 'lbs'])->default('kg');
            $table->string('wake_time', 10)->nullable();
            $table->string('bed_time', 10)->nullable();
            $table->decimal('sleep_hours', 4, 1)->nullable();
            $table->string('sleep_quality', 30)->nullable();
            $table->unsignedInteger('steps')->nullable();
            $table->decimal('water_intake', 5, 2)->nullable();
            $table->enum('water_unit', ['litres', 'glasses'])->default('litres');
            $table->boolean('water_goal_met')->nullable();
            $table->unsignedTinyInteger('mood')->nullable();   // 1 - 5
            $table->unsignedTinyInteger('energy')->nullable(); // 1 - 5
            $table->string('nutrition_adherence', 20)->nullable();
            $table->boolean('movement_done')->nullable();
            $table->string('movement_type')->nullable();
            $table->boolean('meditation_done')->nullable();
            $table->text('meditation_notes')->nullable();
            $table->boolean('journaling_done')->nullable();
            $table->text('journaling_notes')->nullable();
            $table->json('meals')->nullable();
            $table->text('notes')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'record_date']);
        });

        // Baseline & progress measurements + weekly reflections
        Schema::create('weekly_checkins', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->date('record_date');
            $table->boolean('is_baseline')->default(false);
            $table->decimal('weight', 6, 2)->nullable();
            $table->enum('weight_unit', ['kg', 'lbs'])->default('kg');
            $table->decimal('chest', 6, 2)->nullable();
            $table->decimal('abdomen', 6, 2)->nullable();
            $table->decimal('waist', 6, 2)->nullable();
            $table->decimal('hip', 6, 2)->nullable();
            $table->decimal('thigh', 6, 2)->nullable();
            $table->decimal('arm', 6, 2)->nullable();
            $table->enum('measurement_unit', ['cm', 'in'])->default('cm');
            $table->text('self_confidence')->nullable();
            $table->date('last_period_start')->nullable();
            $table->string('cycle_day', 50)->nullable();
            $table->text('cycle_notes')->nullable();
            $table->string('cravings_level', 20)->nullable();
            $table->text('cravings_notes')->nullable();
            $table->boolean('travelled')->nullable();
            $table->text('travel_notes')->nullable();
            $table->text('wins')->nullable();
            $table->text('proud_moments')->nullable();
            $table->text('struggles')->nullable();
            $table->text('notes')->nullable();
            $table->json('extra')->nullable(); // digestion, sleep, stress, habits etc.
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'record_date']);
        });

        Schema::create('progress_photos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->date('record_date');
            $table->string('front_photo')->nullable();
            $table->string('side_photo')->nullable();
            $table->string('back_photo')->nullable();
            $table->json('extra_photos')->nullable();
            $table->boolean('consent')->default(false);
            $table->timestamps();

            $table->unique(['user_uuid', 'record_date']);
        });

        Schema::create('food_diary_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->unsignedTinyInteger('day_number'); // 1 - 3
            $table->enum('day_type', ['weekday', 'weekend'])->default('weekday');
            $table->date('record_date');
            $table->json('meals')->nullable();
            $table->json('sensations')->nullable();
            $table->text('context_notes')->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'day_number']);
        });

        Schema::create('blood_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->date('report_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('blood_report_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('blood_report_uuid')->index();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });

        // Calendar "Notes & Photos"
        Schema::create('daily_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->date('note_date');
            $table->text('note')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'note_date']);
        });

        // Apple Health / Google Fit daily aggregates
        Schema::create('activity_syncs', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid')->index();
            $table->date('activity_date');
            $table->unsignedInteger('steps')->default(0);
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->unsignedInteger('calories')->nullable();
            $table->unsignedInteger('active_minutes')->nullable();
            $table->string('source', 30)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_syncs');
        Schema::dropIfExists('daily_notes');
        Schema::dropIfExists('blood_report_files');
        Schema::dropIfExists('blood_reports');
        Schema::dropIfExists('food_diary_entries');
        Schema::dropIfExists('progress_photos');
        Schema::dropIfExists('weekly_checkins');
        Schema::dropIfExists('daily_checkins');
    }
};
