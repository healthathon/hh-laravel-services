<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserTaskCronStatus
 *
 * This model class represents regular task user status is doing with the detail information of
 * UserTaskCronStatus.
 *
 * @package App\Model
 */
class UserTaskCronStatus extends Model
{
    /**
     * @var array The TypeCasting is done, in order to map data
     */
    protected $table = "user_task_cron_status";

    /**
     * @var array Hidden Items
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
