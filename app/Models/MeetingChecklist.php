<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingChecklist extends Model
{
    protected $fillable = [
        'meeting_minute_id',
        'task_checklist_id',
        'is_completed',
        'remarks',
    ];

    public function meetingMinute()
    {
        return $this->belongsTo(MeetingMinute::class);
    }

    public function taskChecklist()
    {
        return $this->belongsTo(TaskChecklist::class);
    }
}