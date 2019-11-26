<?php

namespace App\Respositories;
use App\Model\Assess\Query;

class QueryRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new Query());
    }

    public function getCountOfQuestionsForGivenTag($tagId)
    {
        return $this->model->where('tag_id', $tagId)->count();
    }

    public function getRequestedTagIdQuestions($id)
    {
        return $this->model->where('tag_id', $id)->get();
    }

}