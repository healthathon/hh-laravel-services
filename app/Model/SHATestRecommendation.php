<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SHATestRecommendation extends Model
{
    protected $table = "sha__test_recommendations";

    protected $fillable = [
        'answer_id',
        'test_id'
    ];
}
