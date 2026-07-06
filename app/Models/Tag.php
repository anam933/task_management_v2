<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(
            Task::class,
            'task_tag',
            'tag_id',
            'task_id'
        )->withTimestamps();
    }

    public function scopeCurrentCategory($query, ?int $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }
        return $query->where('category_id', $categoryId);
    }
}
