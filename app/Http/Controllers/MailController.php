<?php

namespace App\Http\Controllers;

use App\Mail\MMGBookingMail;
use App\Model\User;
use App\Model\MMGBookingMailInfo;
use App\Services\LabService;
use Illuminate\Support\Facades\Mail;
use Log;

/**
 * Class MailController : The class responsible for sending an email to user
 * @package App\Http\Controllers
 * @author Mayank Jariwala
 */
class MailController extends Controller
{

    private $labService;

    public function __construct()
    {
        $this->labService = new LabService();
    }

    //Simply send registration mail with greetings
    public static function sendRegistrationMail($user)
    {
        $name = $user->first_name . " " . $user->last_name;
        Mail::send('layouts.mail.registration', ['name' => $name], function ($message) use ($user, $name) {
            $message->from('no-reply@gmail.com', 'Happily Health');
            $message->to($user->email, $name)
                ->subject('Get, Set, Happily Health');
        });
    }

    //Simply send forgot password mail with new system generated password
    public static function sendForgotPasswordEmail($user, $newPassword)
    {
        $name = $user->first_name . " " . $user->last_name;
        Mail::send('layouts.mail.forgotPassword', ['name' => $name, 'email' => $user->email, 'password' => $newPassword], function ($message) use ($user, $name) {
            $message->from('no-reply@gmail.com', 'Happily Health');
            $message->to($user->email, $name)
                ->subject('Your password has been changed');
        });
    }

    public static function sendEmailVerificationEmail()
    {
    }

    /**
     * @param User $user
     */
    public static function sendMMGBookingMailToUser(User $user)
    {
        $name = $user->first_name . " " . $user->last_name;
        Mail::send('layouts.mail.userbooking', ['name' => $name], function ($message) use ($user, $name) {
            $message->from('no-reply@gmail.com', 'Happily Health');
            $message->to($user->email, $name)
                ->subject('Your Mapmygenome order confirmation');
        });
    }
    public static function sendMMGBookingMailToAdmin(User $user,$data)
    {
//        $name = $user->first_name . " " . $user->last_name;

        $adminEmail = MMGBookingMailInfo::where("to_send","1")->get();

        if(!empty($adminEmail)) {
//            Log::info("Email sent to admin ...");
            $data['subject'] = "Booking For MapMyGenome";

            $to = [];
            foreach ($adminEmail as $admin){
                $to[$admin->user_email] = $admin->user_name;
            }

//            Log:info($data);

            Mail::send('layouts.mail.booking', ['user' => $user, 'data' => $data], function ($message) use ($data, $to) {
                $message->from('no-reply@gmail.com', 'Happily Health');
                $message->to($to)
                    ->subject($data['subject']);
            });
        }
    }
}
