<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMinute extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'meeting_date',
        'title',
        'attendees',
        'discussion_points',
        'decisions',
        'action_items',
        'notes',
        'category_id',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->where(function ($builder) use ($user) {
            $builder->where('user_id', $user->id)
                ->orWhere('category_id', $user->category_id);
        });
    }

    public function scopeCurrentCategory($query, ?int $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }
        return $query->where('category_id', $categoryId);
    }
}
