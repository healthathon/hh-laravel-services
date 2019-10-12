<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NutritionScoreBank extends Model
{
    protected $table = "nutrition_score_banks";

    protected $fillable = [
        "expression",
        "score"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    // Task
    public function recommendation()
    {
        return $this->hasMany("App\Model\NutritionTaskRecommendation", "nutrition_bank_id", "id");
    }
}
