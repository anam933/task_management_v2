<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('meeting_date');
            $table->string('title');
            $table->longText('attendees');
            $table->longText('discussion_points');
            $table->longText('decisions')->nullable();
            $table->longText('action_items')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'meeting_date']);
            $table->index('meeting_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes');
    }
};
