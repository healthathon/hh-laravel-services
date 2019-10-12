<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TagTotalScore extends Model
{
    protected $table = "query__tags_total_scores";

    protected $fillable = [
        "tag_id",
        "score"
    ];

    public function tags()
    {
        return $this->belongsTo('App\Model\Assess\queryTag', 'tag_id', 'id');
    }
}
