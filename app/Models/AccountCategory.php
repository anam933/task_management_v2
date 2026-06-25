<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    protected $fillable = [
        'category_name',
        'category_type',
        'description',
        'status'
    ];

    public function tasks()
        {
            return $this->hasMany(Task::class);
        }
}