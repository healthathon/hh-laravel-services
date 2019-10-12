<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NutritionTaskRecommendation extends Model
{
    protected $table = "nutrition_task_recommendations";

    protected $fillable = [
        "nutrition_bank_id",
        "regimen_id"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];
}
