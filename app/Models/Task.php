<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TaskChecklist;

class Task extends Model
{
    protected $fillable = [
        'task_name',
        'task_details',
        'start_date',
        'deadline_date',
        'priority',
        'assigned_to',
        'assigned_by',
        'reviewer_id',
        'status',
        'task_category_id',
        'project_id',
        'reports_to',
        'submission_remarks',
    ];

    public function tags()
{
    return $this->belongsToMany(
        Tag::class,
        'task_tag',
        'task_id',
        'tag_id'
    )->withTimestamps();
}
    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reportingManager()
        {
            return $this->belongsTo(User::class, 'reports_to');
        }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function checklists()
{
    return $this->hasMany(TaskChecklist::class);
}

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function legacyCategory()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }

    public function scopeVisibleTo($query, User $user)
{
    // Admin -> Everything
    if ($user->hasRole('admin')) {
        return $query;
    }

    // Manager -> Own tasks + Team tasks
    if ($user->hasRole('manager')) {
        return $query->where(function ($builder) use ($user) {

            $builder->where('assigned_to', $user->id)

                ->orWhereHas('assignedUser', function ($employeeQuery) use ($user) {
                    $employeeQuery->where('reports_to', $user->id);
                });

        });
    }

    // Employee -> Only own assigned tasks
    return $query->where('assigned_to', $user->id);
}

    public function scopeCurrentCategory($query, ?int $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }
        return $query->whereHas('project', function ($projectQuery) use ($categoryId) {
            $projectQuery->where('category_id', $categoryId);
        });
    }
}
