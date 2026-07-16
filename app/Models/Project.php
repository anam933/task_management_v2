<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;


class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_name',
        'project_code',
        'project_description',
        'category_id', 
        'start_date',
        'end_date',
        'project_manager_id',
        'project_status',
        'priority',
        'budget',
        'created_by',
        'assigned_to',
        'reports_to',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    // ✅ NEW RELATION
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }


     public function getProgressAttribute()
    {
    $total = $this->tasks()->count();

    if ($total == 0) {
        return 0;
    }

    $completed = $this->tasks()
        ->where('status', 'Completed')
        ->count();

    return round(($completed / $total) * 100);
}

    public function manager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

   public function teamMembers()
{
    return $this->belongsToMany(User::class, 'project_user')
        ->withTimestamps();
}

public function assignedUser()
{
    return $this->belongsTo(User::class, 'assigned_to');
}

    public function activityLogs()
    {
        return $this->hasMany(ProjectActivityLog::class);
    }

    public function reportingManager()
{
    return $this->belongsTo(User::class, 'reports_to');
}




   public function scopeVisibleTo($query, User $user)
{
    // Admin can see everything
    if ($user->hasRole('admin')) {
        return $query;
    }

    // Manager
    if ($user->hasRole('manager')) {
        return $query->where(function ($builder) use ($user) {
            $builder->where('project_manager_id', $user->id)
                    ->orWhere('created_by', $user->id);
        });
    }
    

    // Employee
    return $query->where(function ($builder) use ($user) {
        $builder->where('assigned_to', $user->id)
                ->orWhereHas('teamMembers', function ($teamQuery) use ($user) {
                    $teamQuery->where('users.id', $user->id);
                });
    });
}
    public function scopeCurrentCategory(Builder $query, ?int $categoryId): Builder
{
    if (!$categoryId) {
        return $query;
    }

    return $query->where('category_id', $categoryId);
}




    public function meetingUsers()
{
            $users = collect();

            if ($this->manager) {
                $users->push($this->manager);
            }

            if ($this->assignedUser) {
                $users->push($this->assignedUser);
            }

            if ($this->reportingManager) {
                $users->push($this->reportingManager);
            }

            $users = $users->merge($this->teamMembers);

            return $users->unique('id')->values();
}

}