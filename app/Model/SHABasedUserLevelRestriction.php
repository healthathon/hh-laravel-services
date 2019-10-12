<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SHABasedUserLevelRestriction extends Model
{

    protected $table = "sha__user_level_restrictions";
    protected $fillable = [
        "user_id",
        "restriction_level"
    ];
    protected $hidden = [
        "created_at",
        "updated_at"
    ];
}
