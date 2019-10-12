<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MMGBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user, $testNamesArr;

    /**
     * Create a new message instance.
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("no-reply@gmail.com", 'Happily Health')
            ->subject("User MapMyGenome Test Booking")
            ->markdown('emails.mmp.booking', [
                'testNamesArr' => $this->testNamesArr,
                'name' => $this->user->first_name . " " . $this->user->last_name,
                'email' => $this->user->email
            ]);
    }
}
