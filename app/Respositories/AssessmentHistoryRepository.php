<?php

namespace App\Respositories;


use App\Model\Assess\assesHistory;

class AssessmentHistoryRepository extends BaseRepository
{
    protected $queryTagRepo;
    public function __construct()
    {
        parent::__construct(new assesHistory());

        $this->queryTagRepo = new QueryTagRepository();
    }

    // Returns Tag State ( Good,Bad,Excellent)
    public function getUserTagState($tagName, $user)
    {
        switch ($tagName) {
            case "physics":
                $tagName = "Physical Fitness";
                break;
            case "mental":
                $tagName = "Emotional Well Being";
                break;
            default:
                break;
        }
        $id = $this->queryTagRepo->getTagId($tagName);
        $columnName = "tag" . $id . "_state";
        // return tagX_state value of user
        return $user->assessmentRecord == null ? null : $user->assessmentRecord->$columnName;
    }
}