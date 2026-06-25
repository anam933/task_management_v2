<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $userId = DB::table('users')->value('id');

        $projectId = DB::table('projects')->insertGetId([
            'project_name' => 'General Project',
            'project_code' => 'GEN-001',
            'project_description' => 'Auto-created project used to backfill legacy tasks.',
            'start_date' => $now->toDateString(),
            'end_date' => null,
            'project_manager_id' => $userId,
            'project_status' => 'Planning',
            'priority' => 'Medium',
            'budget' => null,
            'created_by' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('tasks')
            ->whereNull('project_id')
            ->update(['project_id' => $projectId]);
    }

    public function down(): void
    {
        $projectId = DB::table('projects')
            ->where('project_code', 'GEN-001')
            ->value('id');

        if ($projectId) {
            DB::table('tasks')
                ->where('project_id', $projectId)
                ->update(['project_id' => null]);

            DB::table('projects')
                ->where('id', $projectId)
                ->delete();
        }
    }
};
