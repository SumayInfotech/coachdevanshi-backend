<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pathways', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('group', ['core', 'hormonal', 'general'])->default('core');
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('protocol_key', 50)->nullable(); // key in config/protocols.php, null = no protocol form
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        Schema::create('user_pathway_protocols', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->index();
            $table->uuid('pathway_uuid')->index();
            $table->json('answers')->nullable();
            $table->enum('status', ['in_progress', 'submitted'])->default('in_progress');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_uuid', 'pathway_uuid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pathway_protocols');
        Schema::dropIfExists('pathways');
    }
};
