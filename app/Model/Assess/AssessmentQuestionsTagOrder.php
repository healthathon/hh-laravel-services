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

    public static function getRequestedIdOrderSequence($id)
    {
        $assessmentQuestionOrder = AssessmentQuestionsTagOrder::where('id', $id)->first();
        return $assessmentQuestionOrder == null ? self::getActiveOrderSequence() : $assessmentQuestionOrder;
    }

    public static function getActiveOrderSequence()
    {
        return AssessmentQuestionsTagOrder::where('is_active', 1)->first();
    }
}
