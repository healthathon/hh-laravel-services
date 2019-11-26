<?php

namespace App\Model\Assess;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestionsTagOrder extends Model
{

    protected $table = "assessment_questions_tag_orders";
    protected $fillable = [
        'order_seq',
        'is_active'
    ];


}
