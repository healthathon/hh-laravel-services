<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MentalWellBeingLevelMapping extends Model
{

    protected $table = "mental_well_being_level_mappings";

    protected $fillable = [
        "tag",
        "level",
        "state",
        "score"
    ];

    protected $casts = [
        "level" => "int",
        "score" => "int"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];
}
