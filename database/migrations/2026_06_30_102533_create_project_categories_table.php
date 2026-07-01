<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('project_categories')->insert([
            ['category_name' => 'IT', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Business Development', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'HR', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Marketing', 'description' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('project_categories');
    }
};