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
        'category_id',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

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
