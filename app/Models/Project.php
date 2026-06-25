<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_name',
        'project_code',
        'project_description',
        'start_date',
        'end_date',
        'project_manager_id',
        'project_status',
        'priority',
        'budget',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
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
        return $this->belongsToMany(User::class, 'project_user')->withTimestamps();
    }

    public function activityLogs()
    {
        return $this->hasMany(ProjectActivityLog::class);
    }
}
