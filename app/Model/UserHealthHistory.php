<?php

/**
 * This Model Class represents UserHealthHistory Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserHealthHistory
 *
 * This Model Represent History of User Taken During Registration phase
 * in about you section.
 *
 * @package App\Model
 */
class UserHealthHistory extends Model
{
    /**
     * @var string Name of the table
     */
    protected $table = "user_health_histories";

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];
    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id'
    ];

    public function belongsToQuestion()
    {
        return $this->belongsTo('App\Model\ShortHealthAssessment', 'question_id', 'id');
    }

    public function belongsToAnswers()
    {
        return $this->belongsTo('App\Model\SHAQuestionAnswers', 'answer_id', 'id');
    }
}
