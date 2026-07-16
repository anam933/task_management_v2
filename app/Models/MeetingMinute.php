<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingMinute extends Model
{
    protected $table = 'meeting_minutes';

    protected $fillable = [
        'meeting_title',
        'meeting_date',
        'meeting_time',
        'meeting_type',
        'location',
        'agenda',
        'discussion_points',
        'decisions',
        'action_items',
        'project_id',
        'created_by',
        'status',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_participants', 'meeting_minute_id', 'user_id')
            ->withTimestamps();
    }

    public function actions(): HasMany
    {
        return $this->hasMany(MeetingAction::class, 'meeting_minute_id');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('manager')) {
            return $query->where(function ($builder) use ($user) {
                $builder->where('created_by', $user->id)
                    ->orWhereHas('project', function ($projectQuery) use ($user) {
                        $projectQuery->where('project_manager_id', $user->id)
                            ->orWhere('created_by', $user->id);
                    });
            });
        }

        // Employee
        return $query->where(function ($builder) use ($user) {
            $builder->whereIn('status', ['Published', 'Completed'])
                ->whereHas('project', function ($projectQuery) use ($user) {
                    $projectQuery->where('assigned_to', $user->id)
                        ->orWhereHas('teamMembers', function ($teamQuery) use ($user) {
                            $teamQuery->where('users.id', $user->id);
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
