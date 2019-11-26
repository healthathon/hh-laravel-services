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
 * Class MixedBag
 *
 * This model represent mixed bag task information and this model is only use when
 * user has not started assessment or has left assessment incomplete.
 */
class MixedBag extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "mixed_bags";

    /**
     * @var string Column name representing Primary key of Table
     */
    protected $primaryKey = "id";

    /**
     * @var array Represents Hidden Item which not be shown in response
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'regimen_name', 'mapper',
        'day_1', 'day_2',
        'day_3', 'day_4', 'day_5',
        'day_6', 'day_7'
    ];


}
