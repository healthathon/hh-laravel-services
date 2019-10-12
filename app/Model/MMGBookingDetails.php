<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MMGBookingDetails extends Model
{
    protected $table = "mmg_booking_details";
    protected $fillable = [
        "user_id",
        "test_id"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public function users()
    {
        return $this->belongsToMany("App\Model\User");
    }

    public function test()
    {
        return $this->belongsTo("App\Model\LabsTest", "id", "test_id");
    }
}

