<?php

namespace App\Listeners;

use App\Constants;
use App\Events\UpdateUserState;
use App\Model\Assess\Query;
use App\Model\Assess\queryCategory;
use App\Model\Assess\queryTag;
use App\Model\Assess\scoreInterp;
use App\Model\MentalWellBeingLevelMapping;
use App\Model\UsersTaskInformations;

class UpdateUserStateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UpdateUserState $event
     * @return void
     */
    public function handle(UpdateUserState $event)
    {
        $userAssessObject = $event->userAssesObject;
        $data = Query::all()->groupBy('tag_id')->toArray();
        $data = array_keys($data);
        foreach ($data as $value) {
            $userAssessObject = $this->updateUserTagsState($value, $userAssessObject);
        }
        $categoriesGroup = queryTag::all(['id', 'category_id', 'tag_name'])->groupBy('category_id')->toArray();
        // update all category state of user, once user completed the entire assessment
        foreach ($categoriesGroup as $categoryId => $tags) {
            $tagsTotalScoreToSpecificCategories = 0;
            $categoryColumnName = "category" . $categoryId . "_state";
            foreach ($tags as $tag) {
                $tagScoreColumnName = "tag" . $tag['id'] . "_score";
                $tagsTotalScoreToSpecificCategories = $userAssessObject->$tagScoreColumnName + $tagsTotalScoreToSpecificCategories;
            }
            $userAssessObject->$categoryColumnName = $this->updateUserQueryCategoriesState($categoryId, $tagsTotalScoreToSpecificCategories);
        }
        $userAssessObject = $this->updateModuleLevel($userAssessObject);
        $userAssessObject->save();
    }

    private function updateUserTagsState($tagId, $userAssessObject)
    {
        $tagScoreColumnName = "tag" . $tagId . "_score";
        $tagStateColumnName = "tag" . $tagId . "_state";
        $tagInfo = queryTag::where('id', $tagId)->first();
        if ($userAssessObject->$tagScoreColumnName >= $tagInfo->happy_zone_score) {
            $state = Constants::EXCELLENT;
        } else if ($userAssessObject->$tagScoreColumnName >= $tagInfo->work_more_score) {
            $state = Constants::GOOD;
        } else {
            $state = Constants::BAD;
        }
        $userAssessObject->$tagStateColumnName = $state;
        return $userAssessObject;
    }

    private function updateUserQueryCategoriesState($categoryId, $tagsTotalScoreToSpecificCategories)
    {
        // Default State as 3 - Bad
        $categoryState = 3;
        $queryCategoryInfo = queryCategory::find($categoryId)->first();
        if (!is_null($queryCategoryInfo->excellent_marks)) {
            if ($tagsTotalScoreToSpecificCategories >= $queryCategoryInfo->excellent_marks) {
                $categoryState = 1; // Excellent
            } else if ($tagsTotalScoreToSpecificCategories < $queryCategoryInfo->excellent_marks && $tagsTotalScoreToSpecificCategories >= $queryCategoryInfo->good_marks) {
                $categoryState = 2; // Good
            } else if ($tagsTotalScoreToSpecificCategories < $queryCategoryInfo->good_marks) {
                $categoryState = 3; // Bad
            }
        } else {
            if ($tagsTotalScoreToSpecificCategories >= $queryCategoryInfo->good_marks) {
                $categoryState = 2; // Good
            } else if ($tagsTotalScoreToSpecificCategories < $queryCategoryInfo->good_marks) {
                $categoryState = 3; //Bad
            }
        }
        return $categoryState;
    }

    // Each Module (physical,Mental Level will be updated as per assessment results)
    private function updateModuleLevel($userAssessObject)
    {
        if ($userAssessObject->user->taskInformation != null) {
            $userTaskInfo = $userAssessObject->user->taskInformation;
        } else {
            $userTaskInfo = new UsersTaskInformations();
        }
        $scoreInterpObject = scoreInterp::where([
            'category1' => $userAssessObject->category1_state,
            'category2' => $userAssessObject->category2_state,
            'category3' => $userAssessObject->category3_state
        ])->first();
        if (!is_null($scoreInterpObject)) {
            $level = $scoreInterpObject->level;
            $userTaskInfo->user_id = $userAssessObject->user->id;
            $userTaskInfo->physical_level = $level;
            $userTaskInfo->mental_level = $this->getUserMentalLevelFromMentalLevelMappingTable($userAssessObject);
            $userTaskInfo->nutrition_level = $level;
            $userTaskInfo->lifestyle_level = $level;
            $userTaskInfo->save();
        }
        return $userAssessObject;
    }

    private function getUserMentalLevelFromMentalLevelMappingTable($userAssessObject)
    {
        $level = -1;
        $id = queryTag::where("tag_name", Constants::EMOTIONAL_WELL_BEING)->first()->id;
        $column = "tag" . $id . "_score";
        $securedScoredInMentalTag = $userAssessObject->user->assessmentRecord->$column;
        $levelMappingsValue = MentalWellBeingLevelMapping::orderBy("score")->get(["score", "level"]);
        foreach ($levelMappingsValue as $value) {
            if ($securedScoredInMentalTag <= $value->score) {
                $level = $value->level;
                break;
            }
        }
        return $level == -1 ? max(array_column($levelMappingsValue->toArray(), "level")) : $level;
    }
}
