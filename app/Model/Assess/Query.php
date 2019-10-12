<?php

namespace App\Model\Assess;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $table = "queries";
    protected $fillable = [
        'tag_id', 'query', 'results_string', 'results_value', 'is_mental_bank'
    ];

    protected $casts = [
        'is_mental_bank' => "boolean"
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public static function getCountOfQuestionsForGivenTag($tagId)
    {
        return Query::where('tag_id', $tagId)->count();
    }

    public static function getRequestedTagIdQuestions($id)
    {
        return Query::where('tag_id', $id)->get();
    }

    public function tag()
    {
        return $this->belongsTo('App\Model\Assess\queryTag', 'tag_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany('App\Model\AssessmentAnswers', 'query_id', 'id');
    }
}
