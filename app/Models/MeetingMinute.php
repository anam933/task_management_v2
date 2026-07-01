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
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

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
        if ($user->hasRole(['admin', 'manager'])) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }
}
