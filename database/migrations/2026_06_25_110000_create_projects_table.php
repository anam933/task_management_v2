<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('project_code')->unique();
            $table->text('project_description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('project_status')->default('Planning');
            $table->string('priority')->default('Medium');
            $table->decimal('budget', 15, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
