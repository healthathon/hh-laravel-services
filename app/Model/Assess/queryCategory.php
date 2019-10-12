<?php

namespace App\Model\Assess;

use Illuminate\Database\Eloquent\Model;

class queryCategory extends Model
{
    protected $table = "query_categories";
    protected $fillable = [
        'id', 'category_name', 'happy_marks', 'excellent_marks', 'good_marks', 'bad_marks'
    ];

    public function tags()
    {
        return $this->hasMany('App\Model\Assess\queryTag', 'category_id', 'id');
    }
}
