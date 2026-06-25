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
        Schema::create('tasks', function (Blueprint $table) {
    $table->id();

    $table->string('task_name');
    $table->text('task_details')->nullable();

    $table->date('start_date');
    $table->date('deadline_date');

    $table->string('priority')->default('Medium');

    $table->foreignId('assigned_to')->nullable();
    $table->foreignId('assigned_by')->nullable();

    $table->string('status')->default('Pending');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
