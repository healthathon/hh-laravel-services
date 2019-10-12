<?php

/**
 * This Model Class represents UserFriends Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserFriends
 *
 * This model class represents friends of users
 * @package App\Model
 */
class UserFriends extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "users_friends";
    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'user_id',
        'friend_id',
        'status'
    ];

    /**
     * This function all friends of User
     * TODO: This relationship should be in UserModel
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getUserOrFriendInfo()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id');
    }
}
