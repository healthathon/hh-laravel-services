<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BMITestRecommendation extends Model
{
    protected $table = "bmi_test_recommendations";

    protected $fillable = [
        "test_id",
        "answer_id"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public function test()
    {
        return $this->belongsTo("App\Model\LabsTest", "id", "test_id");
    }

    public function deviation()
    {
        return $this->belongsTo("App\Model\BmiScore", "id", "answer_id");
    }
}
