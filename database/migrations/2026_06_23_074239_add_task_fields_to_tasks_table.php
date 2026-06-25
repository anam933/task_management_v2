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
    Schema::table('tasks', function (Blueprint $table) {

        $table->string('task_name')->nullable()->after('id');
        $table->text('task_details')->nullable();

        $table->date('start_date')->nullable();
        $table->date('deadline_date')->nullable();

        $table->string('priority')->default('Medium');

        $table->unsignedBigInteger('assigned_to')->nullable();
        $table->unsignedBigInteger('assigned_by')->nullable();

        $table->string('status')->default('Pending');

    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {

        $table->dropColumn([
            'task_name',
            'task_details',
            'start_date',
            'deadline_date',
            'priority',
            'assigned_to',
            'assigned_by',
            'status'
        ]);

    });
}
};