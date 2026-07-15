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
    Schema::table('projects', function (Blueprint $table) {

        $table->foreignId('reports_to')
            ->nullable()
            ->after('assigned_to')
            ->constrained('users')
            ->nullOnDelete();

    });

    Schema::table('tasks', function (Blueprint $table) {

        $table->foreignId('reports_to')
            ->nullable()
            ->after('assigned_to')
            ->constrained('users')
            ->nullOnDelete();

    });
}

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('projects', function (Blueprint $table) {

        $table->dropForeign(['reports_to']);
        $table->dropColumn('reports_to');

    });

    Schema::table('tasks', function (Blueprint $table) {

        $table->dropForeign(['reports_to']);
        $table->dropColumn('reports_to');

    });
}
};
