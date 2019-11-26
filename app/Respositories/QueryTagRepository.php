<?php

namespace App\Respositories;
use App\Model\Assess\queryTag;

class QueryTagRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new queryTag());
    }

    public function getTagId($name)
    {
        $tagInfo = $this->model->where('tag_name', $name)->first();
        return $tagInfo->id;
    }

    public function getTagName($id)
    {
        $tagInfo = $this->model->where('id', $id)->first(['tag_name']);
        return $tagInfo->tag_name === "Emotional Well Being" ? "Mental Well-Being" : ucfirst($tagInfo->tag_name);
    }

    public function getTagNameFromCache($id)
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

    public function getTagsNameWithId()
    {
        return $this->model->where('tag_name', '<>', 'BMI')
            ->where('tag_name', '<>', 'History')
            ->get(['id', 'tag_name'])->toArray();
    }

}