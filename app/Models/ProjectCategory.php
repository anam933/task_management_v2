<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    protected $fillable = [
        'category_name',
        'description',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }
}