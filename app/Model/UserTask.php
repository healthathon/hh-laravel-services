<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserTask
 *
 * This model class represents regular task user is doing with the detail information of
 * task.
 *
 * @package App\Model
 */
class UserTask extends Model
{
    /**
     * @var array The TypeCasting is done, in order to map data
     */
    protected $casts = [
    ];

    /**
     * @var array Hidden Items
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    /**
     * This function should be in User Model, refactoring need to be done.
     * TODO: Code added by other developer, re-shifting is require [Mayank Jariwala]
     * @return string: Get FullName of User
     */
    public function getFullName()
    {
        return $this->getUserInfoFromId->first_name . " " . $this->getUserInfoFromId->last_name;
    }

    public function getDeviceToken()
    {
        return $this->getUserInfoFromId->device_token;
    }

    /**
     * Get User Information From Id
     * TODO: This function should be in User Model, re-shifting is require [Mayank Jariwala]
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getUserInfoFromId()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id');
    }

    public function taskTracker()
    {
        return $this->hasMany('App\Model\Tasks\UserTaskTracker', 'user_task_id', 'id');
    }

    public function regimenInfo()
    {
        return $this->belongsTo('App\Model\Tasks\taskBank', 'regimen_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\Category', 'regimen_category', 'id');
    }
}
