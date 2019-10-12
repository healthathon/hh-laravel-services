<?php

/**
 * This Model Class represents UserAchievements Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAchievements
 *
 * This model represents information about user achievements which user has achieved by
 * completing various tasks
 *
 * @package App\Model
 */
class UserAchievements extends Model
{

    /**
     * @var string Name of the table
     */
    protected $table = "user_achievements";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'user_id',
        'badge_url'
    ];

    /**
     * @var array Hidden Elements (Not to Show in response)
     */
    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at'
    ];
}
