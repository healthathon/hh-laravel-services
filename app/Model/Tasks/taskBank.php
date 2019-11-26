<?php

namespace App\Model\Tasks;

use Illuminate\Database\Eloquent\Model;

class taskBank extends Model
{

    protected $table = "task_banks";

    protected $fillable = [
        'level',
        'task_name',
        'title',
        'detail',
        'code',
        'category',
        'advise',
        'image'
    ];
    protected $hidden = [
        'image_type',
        'created_at',
        'updated_at'
    ];

    function hasWeeklyTasks()
    {
        return $this->hasMany('App\Model\Tasks\weeklyTask', 'taskBank_id', 'code');
    }

    public function getTaskCategory()
    {
        return $this->hasOne('App\Model\Category', 'id', 'category');
    }

}
