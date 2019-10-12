<?php

namespace App\Model\Assess;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class queryTag extends Model
{
    protected $table = "query__tags_info";

    public static function getTagId($name)
    {
        $tagInfo = queryTag::where('tag_name', $name)->first();
        return $tagInfo->id;
    }

    public static function getTagName($id)
    {
        $tagInfo = queryTag::where('id', $id)->first(['tag_name']);
        return $tagInfo->tag_name === "Emotional Well Being" ? "Mental Well-Being" : ucfirst($tagInfo->tag_name);
    }

    public static function getTagNameFromCache($id)
    {
        $tagName = "";
        $tags = self::getTagsNameWithId();
        foreach ($tags as $tag) {
            if ($tag['id'] == $id) {
                $tagName = $tag['tag_name'];
                break;
            }
        }
        return ucfirst($tagName);
    }

    public static function getTagsNameWithId()
    {
        return queryTag::where('tag_name', '<>', 'BMI')
            ->where('tag_name', '<>', 'History')
            ->get(['id', 'tag_name'])->toArray();
    }

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
