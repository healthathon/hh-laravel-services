<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TestCorrespondingAssessmentQuestionsAnswer extends Model
{
    protected $table = "test_corresponding_assessment_answers";
    protected $primaryKey = "id";

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'answer_id', 'recommended_test'
    ];

    public function answer()
    {
        return $this->belongsTo('App\Model\AssessmentAnswers', 'id', 'answer_id');
    }
}
