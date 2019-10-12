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

    // Returns Tag State ( Good,Bad,Excellent)
    public static function getUserTagState($tagName, $user)
    {
        switch ($tagName) {
            case "physics":
                $tagName = "Physical Fitness";
                break;
            case "mental":
                $tagName = "Emotional Well Being";
                break;
            default:
                break;
        }
        $id = queryTag::getTagId($tagName);
        $columnName = "tag" . $id . "_state";
        // return tagX_state value of user
        return $user->assessmentRecord == null ? null : $user->assessmentRecord->$columnName;
    }

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
