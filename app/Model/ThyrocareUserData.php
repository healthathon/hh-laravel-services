<?php

/**
 * This Model Class represents ThyrocareUserData Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ThyrocareUserData
 *
 * This class represents the User data of Thyrocare Lab like which user has order
 * for which test and all the response are stored in DB sent by Thyrocare API
 *
 * @package App\Model
 */
class ThyrocareUserData extends Model
{
    /**
     * @var string Column name representing Primary key of Table
     */
    protected $primaryKey = "id";

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];
    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'order_id', 'ref_order_id', 'email', 'fasting', 'mobile', 'address', 'booked_by',
        'product', 'rate', 'service_type', 'payment_mode',
        'payment_type', 'order_status', 'hard_copy', 'user_id'
    ];

    // Modifications : Shift this relationship into User Model with getThyrocareData function

    /**
     * Holds an relationship with user table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getUser()
    {
        return $this->belongsTo('App\Model\User', 'id', 'user_id');
    }

    /**
     *  This relation will help to fetch the information of ben details
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getUserBenDetails()
    {
        return $this->hasMany('App\Model\ThyrocareBeanDetails', 'order_id', 'order_id');
    }
}
