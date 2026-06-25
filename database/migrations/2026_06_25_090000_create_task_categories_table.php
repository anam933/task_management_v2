<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('task_categories')->insert([
            ['category_name' => 'Development', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Testing', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Bug Fix', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Design', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Documentation', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('task_categories');
    }
};
