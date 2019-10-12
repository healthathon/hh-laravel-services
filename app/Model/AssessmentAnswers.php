<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AssessmentAnswers extends Model
{
    protected $table = "assessment_answers";
    protected $fillable = [
        'tag_id',
        'query_id',
        'answer',
        'restricted_level'
    ];

    public function queryInfo()
    {
        return $this->hasOne('App\Model\Assess\Query', 'id', 'query_id');
    }

    public function queryTagInfo()
    {
        return $this->hasOne('App\Model\Assess\queryTag', 'id', 'tag_id');
    }

    public function recommend_regimen()
    {
        return $this->hasMany('App\Model\TaskCorrespondingAssessmentAnswers', 'answer_id', 'id');
    }

    public function recommend_test()
    {
        return $this->hasMany('App\Model\TestCorrespondingAssessmentQuestionsAnswer', 'answer_id', 'id');
    }
}
