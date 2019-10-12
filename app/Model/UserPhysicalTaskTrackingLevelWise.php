<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPhysicalTaskTrackingLevelWise extends Model
{

    protected $table = "user_physical_task_tracking_level_wises";

    protected $fillable = [
        "user_id",
        "task_completed",
        "level"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public function user()
    {
        return $this->belongsTo("App\Model\User", "user_id", "id");
    }
}
