<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LongAssessUserLevelRestriction extends Model
{
    protected $table = "long_assess_user_level_restrictions";
    protected $fillable = [
        "user_id",
        "restriction_level"
    ];
    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public function user()
    {
        return $this->belongsTo("App\Model\User", "user_id", "id");
    }

}
