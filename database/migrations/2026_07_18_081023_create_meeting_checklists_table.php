<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_checklists', function (Blueprint $table) {

            $table->id();

            $table->foreignId('meeting_minute_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('task_checklist_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->boolean('is_completed')->default(false);

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_checklists');
    }
};