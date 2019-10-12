<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MMGBookingMailInfo extends Model
{
    protected $table = "mmg_booking_mail_infos";

    protected $fillable = [
        "user_name",
        "user_email",
        "to_send"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];
}
