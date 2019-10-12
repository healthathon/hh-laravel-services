<?php

namespace App\Http\Controllers\Admin;

use App\Constants;
use App\Model\Assess\Query;
use App\Model\Assess\queryTag;
use App\Model\AssessmentAnswers;
use App\Services\AssessmentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MentalBankRecommendation extends Controller
{

    private $assessmentService;

    public function __construct()
    {
        $this->assessmentService = new AssessmentService();
    }

    public function getPage()
    {
        return view("admin.mental_bank.index");
    }

    public function getAll()
    {
        try {
            $tagId = queryTag::getTagId(Constants::EMOTIONAL_WELL_BEING);
            $queries = Query::where("is_mental_bank", 0)
                ->where("tag_id", $tagId)
                ->get();
            $customizeDataArr = [];
            foreach ($queries as $query) {
                foreach ($query->answers as $answer) {
                    $customizeDataArr[] = [
                        'query' => $query->query,
                        'query_id' => $query->id,
                        'recommended_regimen' => $this->mapTaskWithId($answer),
                        'answer' => $answer->answer,
                    ];
                }
            }
            return $customizeDataArr;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function mapTaskWithId(AssessmentAnswers $answer)
    {
        $regimenIds = array_column($answer->recommend_regimen->toArray(), 'recommended_regimen');
        $regimenNames = null;
        if (count($regimenIds) > 0) {
            $regimenNames = $this->assessmentService->mapTaskNameWithIds($regimenIds);
            $regimenNames = count($regimenNames) > 0 ? implode(",", $regimenNames) : null;
        }
        return $regimenNames;
    }
}
