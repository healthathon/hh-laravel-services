<?php

/**
 * This Model Class represents ThyrocareBenDetails Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ThyrocareBeanDetails
 * This model class holds information about patients who are taking test through thyrocare
 * labs.
 * @package App\Model
 */
class ThyrocareBeanDetails extends Model
{

    /**
     * @var string Column name representing Primary key of Table
     */
    protected $primaryKey = "id";

    /**
     * @var string Name of the table
     */
    protected $table = "thyrocare_bean_details";

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'order_id',
        'lead_id',
        'name',
        'gender',
        'age'
    ];
}
