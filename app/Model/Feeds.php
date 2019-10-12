<?php

/**
 * This Model Class represents Feeds Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Feeds
 *
 * Holds feeds of user ( User  Completing Task)
 */
class Feeds extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "feeds";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        "id",
        "user_id",
        "task",
        "day",
        "badge",
        "week",
        "created_at",
        "updated_at"
    ];

    /**
     * Avoid adding duplicate entry into feeds table
     *
     * @param $user_id : User Identification Number
     * @param $week_number : Week Number of Task Completion
     * @param $task_number :  Id of Task Completed by User
     * @return bool: Returns whether such entry exists
     */
    public static function checkEntryExists($user_id, $week_number, $task_number)
    {
        $recordCount = Feeds::where('user_id', $user_id)->where('week', $week_number)->where('task', $task_number)->count();
        if ($recordCount != 0)
            return true;
        else
            return false;
    }

    /**
     * This function holds a relationship with task in order to get task information from id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getTaskInfo()
    {
        return $this->hasOne('App\Model\Tasks\taskBank', 'id', 'task');
    }

    /**
     * This function holds a relationship with users table in order to get user information from user_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getUserInfo()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id');
    }

}
