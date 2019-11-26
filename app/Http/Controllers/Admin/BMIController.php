<?php

namespace App\Http\Controllers\Admin;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BmiScore;
use App\Model\LabsTest;
use App\Respositories\BmiScoreRepository;
use App\Respositories\LabRepository;
use App\Services\AssessmentService;
use App\Services\BMIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BMIController extends Controller
{
    private $bmiServiceObj, $assessmentService, $bmiScoreRepo;

    public function __construct(BMIService $bmiServiceObj)
    {
        $this->bmiServiceObj = $bmiServiceObj;
        $this->assessmentService = new AssessmentService();
        $this->bmiScoreRepo = new BmiScoreRepository();
    }

    public function testRecommendationPage()
    {
        return view('admin.bmi.recommendTest');
    }

    public function fetchRecommendedTests()
    {
        $bmiEntries = $this->bmiServiceObj->bmiScores();
        $customizeDataArr = [];
        foreach ($bmiEntries as $bmiEntry) {
            $testIds = $bmiEntry->recommend_test == null ? [] : array_column($bmiEntry->recommend_test->toArray(), 'test_id');
            $recommendedTestNames = null;
            if (count($testIds) > 0) {
                $recommendedTestNames = $this->assessmentService->mapTestNameWithTestIds($testIds);
                $recommendedTestNames = count($recommendedTestNames) > 0 ? implode(",", $recommendedTestNames) : null;
            }
            $customizeDataArr[] = [
                'id' => $bmiEntry->id,
                'deviation_range' => $bmiEntry->deviation_range,
                'recommended_test' => $recommendedTestNames
            ];
        }
        return $customizeDataArr;
    }

    public function updateTestRecommendationPage($bmiId)
    {
        $bmiObj = $this->bmiScoreRepo->where("id", $bmiId)->first();
        // RecommendedTest : Test recommend to corresponding answers
        $recommendIds = $bmiObj->recommend_test;
        $recommendedIds = $recommendIds == null ? [] : array_column($recommendIds->toArray(), 'test_id');
        $labTests = (new LabRepository())->all();
        return view('admin.bmi.modifyRecommendTest', compact('bmiObj',
            'labTests', 'recommendedIds'));
    }

    public function updateTestRecommendations(Request $request)
    {
        $bmiRefId = $request->get('bmi_id');
        $testIds = $request->get('recommended_test');
        $testIds = is_array($testIds) ? $testIds : [];
        $newRecommendedTest = [];
        foreach ($testIds as $testId) {
            $newRecommendedTest[] = [
                'test_id' => $testId,
                'answer_id' => $bmiRefId,
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s")
            ];
        }
        DB::beginTransaction();
        try {
            DB::transaction(function () use ($bmiRefId, $newRecommendedTest) {
                $bmiObject = $this->bmiScoreRepo->where("id", $bmiRefId)->first();
                $bmiObject->recommend_test()->delete();
                $bmiObject->recommend_test()->insert($newRecommendedTest);
            });
            DB::commit();
            return ["data" => "Recommendation Test Updated Successfully"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function showBMIScoresPage()
    {
        return view('admin.bmi.index');
    }

    public function getBMIScores()
    {
        return $this->bmiServiceObj->bmiScores();
    }

    public function updateBMIScore(Request $request)
    {
        $validate = Validator::make($request->all(), ['score' => 'required']);
        if ($validate->fails())
            return Helpers::sendResponse(["error" => $validate->getMessageBag()->first()]);
        try {
            $this->bmiServiceObj->updateBMIScore($request->all());
            return Helpers::sendResponse(["data" => "BMI Score updated"]);
        } catch (\Exception $e) {
            return Helpers::sendResponse(["error" => $e->getMessage()]);
        }
    }
}
