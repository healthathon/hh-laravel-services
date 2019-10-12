<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BmiScore extends Model
{
    protected $table = "bmi_scores";

    protected $fillable = [
        "deviation_range",
        "score"
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function recommend_test()
    {
        return $this->hasMany("App\Model\BMITestRecommendation", "answer_id", "id");
    }
}
