<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShortHealthAssessment extends Model
{
    protected $table = "short_health_assessments";

    protected $fillable = [
        'header', 'question', 'multiple'
    ];

    protected $attributes = [
        'multiple' => true
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function answers()
    {
        return $this->hasMany('App\Model\SHAQuestionAnswers', 'question_id', 'id');
    }
}
