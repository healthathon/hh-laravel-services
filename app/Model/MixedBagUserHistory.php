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


}
