<?php

namespace App\Model\Tasks;

use Illuminate\Database\Eloquent\Model;

class UserTaskTracker extends Model
{
    protected $table = "user_task_trackers";

    protected $casts = [
        'days_status' => 'array'
    ];
    protected $fillable = [
        'user_task_id', 'week_status', 'week_percentage',
        'week', 'days_status'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    public function taskBasicInfo()
    {
        return $this->belongsTo('App\Model\UserTask', 'user_task_id', 'id');
    }
}
