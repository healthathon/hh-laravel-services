<?php

namespace App\Model\Tasks;

use Illuminate\Database\Eloquent\Model;

class weeklyTask extends Model
{
    protected $table = "weekly_tasks";

    protected $fillable = [
        'taskBank_id', 'week',
        'day1_title', 'day1_message', 'day1_badge',
        'day2_title', 'day2_message', 'day2_badge',
        'day3_title', 'day3_message', 'day3_badge',
        'day4_title', 'day4_message', 'day4_badge',
        'day5_title', 'day5_message', 'day5_badge',
        'day6_title', 'day6_message', 'day6_badge',
        'day7_title', 'day7_message', 'day7_badge',
        'image', 'week_detail', 'x', 'y', 'badge'
    ];

    public static function getTaskTotalWeeks($task_id)
    {
        return weeklyTask::where('taskBank_id', $task_id)->count();
    }

    public static function getDayCompleteMessage($day, $weekNo, $taskBankId)
    {
        $column = "day" . $day . "_message";
        $data = weeklyTask::where('week', $weekNo)
            ->where('taskBank_id', $taskBankId)
            ->first([$column]);
        return $data->$column;
    }
}
