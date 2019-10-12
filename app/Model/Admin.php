<?php

/**
 * This Model Class represents Admin Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 */

namespace App\Model;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class Admin
 *
 * Represents Admin Authentication Details Table
 */
class Admin extends Authenticatable
{

    use Notifiable;

    /**
     * @var string  Admin Model Table Name
     */
    protected $table = "admins";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Avoid Sending this field value in response to client
     */
    protected $hidden = [
        'remember_token'
    ];
}
