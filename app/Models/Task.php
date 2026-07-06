<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function legacyCategory()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole(['admin', 'manager'])) {
            return $query;
        }

        return $query->where(function ($builder) use ($user) {
            $builder->where('assigned_to', $user->id)
                ->orWhereHas('project', function ($projectQuery) use ($user) {
                    $projectQuery->where('project_manager_id', $user->id)
                        ->orWhere('created_by', $user->id)
                        ->orWhereHas('teamMembers', function ($teamQuery) use ($user) {
                            $teamQuery->whereKey($user->id);
                        });
                });
        });
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
