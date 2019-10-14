<?php

namespace App\Http\Controllers;

use App\Mail\MMGBookingMail;
use App\Services\LabService;
use Illuminate\Support\Facades\Mail;

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
                ->subject('Happily Health - Password Reset');
        });
    }

    public static function sendEmailVerificationEmail()
    {
    }
}
