<?php

/**
 * This Model Class represents Blog Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MixedBagUserHistory
 *
 * This model class represents the user history of mixed bag task
 * @package App\Model
 */
class MixedBagUserHistory extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "mixed_bag_user_histories";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'user_id',
        'regimen_id',
        'user_history'
    ];

    /**
     * @var array Hidden Variable (Not showing in response)
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * This function fetch user object who is doing MixedBag Task
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return $mixed|null:  Return Object of user or null
     */
    public static function getUserMbObject($userId, $regimenId)
    {
        if (!self::userDoingRegimen($userId, $regimenId))
            return null;
        else
            return MixedBagUserHistory::where('user_id', $userId)->where('regimen_id', $regimenId)->first();
    }

    /**
     * This function checks whether user is doing an regimen
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return mixed
     */
    public static function userDoingRegimen($userId, $regimenId)
    {
        return MixedBagUserHistory::where('user_id', $userId)->where('regimen_id', $regimenId)->exists();
    }

    /**
     * This function fetch user doing mixed bag tasks
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return mixed
     */
    public static function getUserDoingTasks($userId, $regimenId)
    {
        return MixedBagUserHistory::where('user_id', $userId)
            ->where('category', $regimenId)
            ->pluck('regimen_id')
            ->toArray();
    }

    /**
     * This function removes/unregister user from mixed bag task
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return mixed
     */
    public static function removeUser($userId, $regimenId)
    {
        return MixedBagUserHistory::where('user_id', $userId)
            ->where('regimen_id', $regimenId)
            ->delete();
    }
}
