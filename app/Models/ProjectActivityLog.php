<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectActivityLog extends Model
{
    protected $fillable = [
        'project_id',
        'task_id',
        'category_id',
        'user_id',
        'event',
        'title',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrentCategory($query, ?int $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }
        return $query->where('category_id', $categoryId);
    }
}
