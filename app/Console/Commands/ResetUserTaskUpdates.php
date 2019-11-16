<?php

namespace App\Console\Commands;

use App\Helpers;
use App\Model\UserTask;
use App\Model\UserTaskCronStatus;
use App\Services\TaskServices;
use Illuminate\Console\Command;
use DB;
class ResetUserTaskUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-task-tracker';
    private $helpers;
    private $taskService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If user not update his task more then 1 week he will be back to previous week';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->helpers = new Helpers();
        $this->taskService = new TaskServices();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(str_repeat("~=~", 20));
        $this->info("Getting users tasks...");

        $skip = 0;
        $userTaskCronStatus = UserTaskCronStatus::orderBy("id")->first();
        if (!empty($userTaskCronStatus)) {
            $skip = $userTaskCronStatus->last_record_count;
        }

        $userTasks = UserTask::limit(10)->skip($skip)->orderBy("id", "ASC")->get();

//        dd($userTasks->toArray());

        $response = [];
        if (!empty($userTasks) && count($userTasks) > 0) {
            foreach ($userTasks as $userTaskDetails) {
                if ($userTaskDetails->last_done_date != '' && !empty($userTaskDetails->last_done_date) && $userTaskDetails->last_done_date != null) {

                    $new_start_date = $userTaskDetails->new_start_date;
                    $reset_week_counter = $userTaskDetails->reset_week_counter;

                    $dateDifference = date_diff($this->helpers->date, new \DateTime($new_start_date));
                    $day = ($dateDifference->days % 7) + 1;
                    $predicted_week_number = (int)($dateDifference->days / 7) + 1;
                    $predicted_today = $day;

                    if ($predicted_week_number > 1) {

                        $response[$userTaskDetails->id]['date'] = date("Y-m-d H:i:s");
                        $response[$userTaskDetails->id]['predicted_week_number'] = $predicted_week_number;
                        $response[$userTaskDetails->id]['predicted_today'] = $predicted_today;
                        $response[$userTaskDetails->id]['last_done_date'] = $new_start_date;
                        $response[$userTaskDetails->id]['task_start_date'] = $userTaskDetails->start_date;

                        if ($reset_week_counter == 2) {
                            $taskTracker1 = $userTaskDetails->taskTracker()->where('week', $predicted_week_number - 1)->orderBy('week', "DESC")->first();
                            if (!empty($taskTracker1)) {
                                $response[$userTaskDetails->id]['removed_last_tracker_id'] = $taskTracker1->id;
                                $taskTracker1->delete();
                            }
                            $taskTracker2 = $userTaskDetails->taskTracker()->where('week', $predicted_week_number - 2)->orderBy('week', "DESC")->first();

                            if (!empty($taskTracker2)) {
                                $taskTracker2->days_status = array_fill(0, 6, 0);
                                $taskTracker2->week_percentage = 0;
                                $taskTracker2->save();
                                $response[$userTaskDetails->id]['reset_tracker'] = $taskTracker2->id;
                            }

                        } else {
                            $taskTrackerObj = $userTaskDetails->taskTracker()->where('week', $predicted_week_number - $reset_week_counter)->orderBy('week', "DESC")->first();

                            if (!empty($taskTrackerObj)) {
                                $taskTrackerObj->days_status = array_fill(0, 6, 0);
                                $taskTrackerObj->week_percentage = 0;
                                $taskTrackerObj->save();
                                $response[$userTaskDetails->id]['reset_tracker'] = $taskTrackerObj->id;
                            }
                        }

                        $differenceDays = ($predicted_week_number*7) - ($reset_week_counter*7);
                        $date = $this->helpers->date;

                        $start_date =  date('Y-m-d',(strtotime ( '-'.$differenceDays.' day' , strtotime ( $date) ) ));;
                        $userTaskDetails->new_start_date = $start_date;
                        $userTaskDetails->reset_week_counter = $reset_week_counter == 2 ? 1 : 2;
                        $userTaskDetails->save();

                        $response[$userTaskDetails->id]['start_date'] = $start_date;

                    } else {

                        if ($predicted_today == 7) {
                            // need to send notification here to user
                        }

                    }

                }
            }

            DB::table('user_task_cron_status')->where('id', 1)->increment('last_record_count', count($userTasks));
        }else{
            DB::table('user_task_cron_status')->where('id', 1)->update(['last_record_count'=>0]);
        }


//        $myfile = fopen("reset_job_log.txt", "w");
        $txt = str_repeat("~=~",30)."\n";
//        fwrite($myfile, $txt);
        $txt .= "Date : ".date("Y-m-d H:i:s")."\n";
//        fwrite($myfile, $txt);
        $txt .= str_repeat("~=~",30)."\n";
//        fwrite($myfile, $txt);
        $txt .= "\n".print_r($response,true)."\n";
//        fwrite($myfile, $txt);
        $txt .= str_repeat("~=~",30)."\n\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        $path = public_path('reset_job_log.txt');

        $myfile = file_put_contents($path, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

//        print_r($userTasks->toArray());
        $this->info(print_r($response));


        $this->info(str_repeat("~=~", 20));
    }
}
