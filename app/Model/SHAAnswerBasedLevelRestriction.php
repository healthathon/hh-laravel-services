<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SHAAnswerBasedLevelRestriction extends Model
{

    protected $table = "sha__answer_level_restrictions";
    protected $fillable = [
        "sha_answer_id",
        "restriction_level"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public function answer()
    {
        return $this->belongsTo("App\Model\SHAQuestionAnswers", "sha_answer_id", "id");
    }
}
