<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'status',
        'task_category_id',
        'project_id',
    ];

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function legacyCategory()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }
}
