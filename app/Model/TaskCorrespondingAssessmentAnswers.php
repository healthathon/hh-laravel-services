<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TaskCorrespondingAssessmentAnswers extends Model
{
    protected $table = "task_corresponding_assessment_answers";

    protected $fillable = [
        'answer_id',
        'recommended_regimen'
    ];

    public function answer()
    {
        return $this->belongsTo('App\Model\AssessmentAnswers', 'id', 'answer_id');
    }
}
