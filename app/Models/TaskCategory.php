<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model
{
    protected $fillable = [
        'category_name',
        'description',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_category_id');
    }
}
