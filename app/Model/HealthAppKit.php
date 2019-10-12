<?php

/**
 * This Model Class represents HealthAppKit Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Model
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HealthAppKit
 *
 * Represents Information about user daily life counts of heartbeat, sleep, walk,bmi etc
 */
class HealthAppKit extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "health_app_kits";

    /**
     * @var string Column name representing Primary key of Table
     */
    protected $primaryKey = "id";

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        "user_id", "steps", "steps_date", "sleep", "walk",
        "walk_date", "heart_rate", "heart_rate_date", "bmi", "bmi_date",
        "temp", "temp_date", "fate", "fate_date", "start_date", "end_date",
        "created_at"
    ];
}
