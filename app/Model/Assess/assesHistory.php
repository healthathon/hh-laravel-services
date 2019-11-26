<?php

namespace App\Model\Assess;

use Illuminate\Database\Eloquent\Model;

class assesHistory extends Model
{

    protected $table = "user_assessment_records";

    protected $casts = [
        'recommended_test' => 'array',
        'tags_completed' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

    public function delete()
    {
        $this->hasManyUserAssessmentAnswers()->delete();
        return parent::delete();
    }

    public function hasManyUserAssessmentAnswers()
    {
        return $this->hasMany('App\Model\Assess\UserAssessmentAnswers', 'user_assess_id', 'id');
    }

    public function orderSeq()
    {
        return $this->hasOne('App\Model\Assess\AssessmentQuestionsTagOrder', 'id', 'order_seq_id');
    }
}
