<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SHAQuestionAnswers extends Model
{
    protected $table = "sha_question_answers";

    protected $fillable = [
        'question_id',
        'answer',
        'score'
    ];

    public function belongToQuestion()
    {
        return $this->belongsTo('App\Model\ShortHealthAssessment', 'question_id', 'id');
    }

    public function restriction()
    {
        return $this->hasOne("App\Model\SHAAnswerBasedLevelRestriction", "sha_answer_id", "id");
    }

    public function recommendedRegimens()
    {
        return $this->hasMany('App\Model\SHATaskRecommendation', 'answer_id', 'id');
    }

    public function recommendedTests()
    {
        return $this->hasMany('App\Model\SHATestRecommendation', 'answer_id', 'id');
    }
}
