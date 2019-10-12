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

    static function getTaskName($taskId)
    {
        $taskObject = taskBank::find($taskId);
        // Handle Null case later on
        return $taskObject != null ? $taskObject->task_name : "null";
    }

    static function getTaskBankObject($taskId)
    {
        return taskBank::where('id', $taskId)->first();
    }

    //data = Array
    public static function updateTaskBank($id, $data)
    {
        $updateResponse = taskBank::where('id', $id)->update($data);
        return $updateResponse;
    }

    public static function deleteTaskBank($id)
    {
        $deleteResponse = taskBank::where('id', $id)->delete();
        return $deleteResponse;
    }

    function hasWeeklyTasks()
    {
        return $this->hasMany('App\Model\Tasks\weeklyTask', 'taskBank_id', 'code');
    }

    public function getTaskCategory()
    {
        return $this->hasOne('App\Model\Category', 'id', 'category');
    }

}
