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
        // Drop existing tables if they exist to prevent conflicts and ensure clean state
        Schema::dropIfExists('meeting_actions');
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('meeting_minutes');

        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_title');
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->string('meeting_type'); // Online/Offline
            $table->string('location')->nullable();
            $table->text('agenda')->nullable();
            $table->longText('discussion_points');
            $table->longText('decisions')->nullable();
            $table->longText('action_items')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('Draft'); // Draft/Published/Completed
            $table->timestamps();
        });

        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_minute_id')->constrained('meeting_minutes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('meeting_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_minute_id')->constrained('meeting_minutes')->cascadeOnDelete();
            $table->string('action_title');
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->date('deadline');
            $table->string('status')->default('Pending'); // Pending/In Progress/Completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_actions');
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('meeting_minutes');
    }
};
