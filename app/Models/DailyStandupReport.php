<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStandupReport extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'report_date',
        'yesterday_work',
        'today_plan',
        'blockers',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
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
