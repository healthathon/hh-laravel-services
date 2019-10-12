<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SHATaskRecommendation extends Model
{
    protected $table = "sha__task_recommendations";

    protected $fillable = [
        'answer_id',
        'task_id'
    ];

    public function answers()
    {
        return $this->belongsTo("App\Model\SHAQuestionAnswers", "answer_id", "id");
    }

    public function regimens()
    {
        return $this->belongsTo("App\\Model\\Tasks\\taskBank", "task_id", "id");
    }
}
