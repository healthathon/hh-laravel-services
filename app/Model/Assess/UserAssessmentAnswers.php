<?php

namespace App\Model\Assess;

use Illuminate\Database\Eloquent\Model;

class UserAssessmentAnswers extends Model
{

    protected $table = "user_assessment_answers";

    protected $fillable = [
        'user_assess_id',
        'tag_id',
        'query_id',
        'answer',
        'score'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function userAssessmentRecord()
    {
        return $this->belongsTo('App\Model\Assess\assesHistory', 'user_assess_id', 'id');
    }
}
