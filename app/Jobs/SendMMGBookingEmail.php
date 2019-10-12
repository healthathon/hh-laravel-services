<?php

namespace App\Jobs;

use App\Mail\MMGBookingMail;
use App\Model\MMGBookingMailInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMMGBookingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $user, $testNamesArr;

    /**
     * Create a new job instance.
     *
     * @param $user
     * @param $testNamesArr
     */
    public function __construct($user, $testNamesArr)
    {
        $this->testNamesArr = $testNamesArr;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $receiversMailInfo = MMGBookingMailInfo::where('to_send', 1)->pluck('user_email');
        if (count($receiversMailInfo) > 0) {
            foreach ($receiversMailInfo as $mailInfo)
                Mail::to($mailInfo)->send(new MMGBookingMail($this->user, $this->testNamesArr));
        } else {
            Mail::to("darshan.sector7@gmail.com")->send(new MMGBookingMail($this->user, $this->testNamesArr));
        }
    }
}
