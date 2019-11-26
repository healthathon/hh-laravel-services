<?php

namespace App\Model\Assess;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class queryTag extends Model
{
    protected $table = "query__tags_info";

    public function overallScore()
    {
        return $this->hasOne('App\Model\TagTotalScore', 'tag_id', 'id');
    }

    public function getQueriesCategoriesInfo()
    {
        return $this->hasOne('App\Model\Assess\queryCategory', 'id', 'category_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\Assess\queryCategory', 'category_id', 'id');
    }
}
