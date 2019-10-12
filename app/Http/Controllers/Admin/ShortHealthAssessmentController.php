<?php

namespace App\Http\Controllers\Admin;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Assess\queryTag;
use App\Model\LabsTest;
use App\Model\SHAAnswerBasedLevelRestriction;
use App\Model\SHAQuestionAnswers;
use App\Model\ShortHealthAssessment;
use App\Model\Tasks\taskBank;
use App\Services\SHAServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShortHealthAssessmentController extends Controller
{
    private $shaServiceObj;

    public function __construct(SHAServices $shaServiceObj)
    {
        $this->shaServiceObj = $shaServiceObj;
    }

    public function getQuestions()
    {
        return $this->shaServiceObj->questions();
    }

    public function testRecommendPage()
    {
        return view("admin.shortassess.recommendTest");
    }

    public function getQuestionForTestRecommendationPage()
    {
        $questions = ShortHealthAssessment::all();
        $formattedQuestionData = [];
        foreach ($questions as $question) {
            foreach ($question->answers as $key => $answer) {
                $recommendedIds = array_column($answer->recommendedTests->toArray(), "test_id");
                $testsName = LabsTest::whereIn("id", $recommendedIds)->pluck("test_name")->toArray();
                $formattedQuestionData[] = [
                    'id' => $question->id,
                    'header' => $question->header,
                    'question' => $question->question,
                    'answer' => $answer->answer,
                    'recommended_test' => join(",", array_unique($testsName)),
                    'update_recommend_test' => route("admin.sha.test.recommend.info.get", ["questionId" => $question["id"]])
                ];
            }
        }
        return $formattedQuestionData;
    }

    public function testRecommendAnswerPage($questionId)
    {
        $query = ShortHealthAssessment::where("id", $questionId)->first();
        $tests = LabsTest::all();
        $questionAnswerObject = $query->answers()->first();
        $recommendedIds = array_column($questionAnswerObject->recommendedTests->toArray(), "test_id");
        return view("admin.shortassess.modifyRecommenTest", compact("query", "tests", "recommendedIds"));
    }

    public function updateRecommendedTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query_id' => "required",
            'answer_id' => "required"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        $questionId = $request->get("query_id");
        $answerId = $request->get("answer_id");
        $recommend_tests = $request->get("recommended_test");
        $recommend_tests = is_array($recommend_tests) ? $recommend_tests : [];
        try {
            DB::beginTransaction();
            $shaObject = ShortHealthAssessment::where('id', $questionId)->first();
            $questionAnswerObject = $shaObject->answers->where("id", $answerId)->first();
            $existingRecommendationTestIds = array_column($questionAnswerObject->recommendedTests->toArray(), "test_id");
            $existingRecommendationTaskIds = count($existingRecommendationTestIds) > 0 ? $existingRecommendationTestIds : $recommend_tests;
            $removedList = array_diff($existingRecommendationTaskIds, $recommend_tests);
            $questionAnswerObject->recommendedTests()->whereIn("test_id", $removedList)->delete();
            foreach ($recommend_tests as $recommend_test) {
                $recommendTestsCollection = [
                    'answer_id' => $answerId,
                    'test_id' => $recommend_test,
                    'created_at' => date("y-m-d h:i:s"),
                    'updated_at' => date("y-m-d h:i:s"),
                ];
                $questionAnswerObject->recommendedTests()->updateOrCreate([
                    'answer_id' => $answerId, 'test_id' => $recommend_test
                ], $recommendTestsCollection);
            }
            DB::commit();
            return ["data" => "Success"];
        } catch (\Exception $exception) {
            DB::rollBack();
            return ["error" => $exception->getMessage()];
        }
    }

    public function taskRecommendPage()
    {
        return view("admin.shortassess.recommendRegimen");
    }

    public function getQuestionForTaskRecommendationPage()
    {
        $questions = ShortHealthAssessment::all();
        $formattedQuestionData = [];
        foreach ($questions as $question) {
            foreach ($question->answers as $key => $answer) {
                $recommendedIds = array_column($answer->recommendedRegimens->toArray(), "task_id");
                $regimensName = taskBank::whereIn("id", $recommendedIds)->pluck("task_name")->toArray();
                $formattedQuestionData[] = [
                    'id' => $question->id,
                    'header' => $question->header,
                    'question' => $question->question,
                    'answer' => $answer->answer,
                    'recommended_regimen' => join(",", array_unique($regimensName)),
                    'update_recommend_regimen' => route("admin.sha.task.recommend.info.get", ["questionId" => $question["id"]])
                ];
            }
        }
        return $formattedQuestionData;
    }

    public function taskRecommendAnswerPage($questionId)
    {
        $query = ShortHealthAssessment::where("id", $questionId)->first();
        $tagObject = queryTag::where("tag_name", ucfirst("history"))->first();
        $regimens = taskBank::where('id', '<>', $tagObject->id)->get();
        $questionAnswerObject = $query->answers()->first();
        $recommendedIds = array_column($questionAnswerObject->recommendedRegimens->toArray(), "task_id");
        return view("admin.shortassess.modifyRecommendRegimen", compact("query", "regimens", "recommendedIds"));
    }

    public function updateRecommendedTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query_id' => "required",
            'answer_id' => "required"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        $questionId = $request->get("query_id");
        $answerId = $request->get("answer_id");
        $recommend_regimens = $request->get("recommended_regimen");
        $recommend_regimens = is_array($recommend_regimens) ? $recommend_regimens : [];
        try {
            DB::beginTransaction();
            $shaObject = ShortHealthAssessment::where('id', $questionId)->first();
            $questionAnswerObject = $shaObject->answers->where("id", $answerId)->first();
            $existingRecommendationTaskIds = array_column($questionAnswerObject->recommendedRegimens->toArray(), "task_id");
            $existingRecommendationTaskIds = count($existingRecommendationTaskIds) > 0 ? $existingRecommendationTaskIds : $recommend_regimens;
            $removedList = array_diff($existingRecommendationTaskIds, $recommend_regimens);
            $questionAnswerObject->recommendedRegimens()->whereIn("task_id", $removedList)->delete();
            foreach ($recommend_regimens as $recommend_regimen) {
                $recommendRegimensCollection = [
                    'answer_id' => $answerId,
                    'task_id' => $recommend_regimen,
                    'created_at' => date("y-m-d h:i:s"),
                    'updated_at' => date("y-m-d h:i:s"),
                ];
                $questionAnswerObject->recommendedRegimens()->updateOrCreate([
                    'answer_id' => $answerId, 'task_id' => $recommend_regimen
                ], $recommendRegimensCollection);
            }
            DB::commit();
            return ["data" => "Success"];
        } catch (\Exception $exception) {
            DB::rollBack();
            return ["error" => $exception->getMessage()];
        }
    }

    public function fetchTaskRecommendAnswerInfo($questionId, $answerId)
    {
        $shaObject = ShortHealthAssessment::where('id', $questionId)->first();
        $questionAnswerObject = $shaObject->answers->where("id", $answerId)->first();
        $tagObject = queryTag::where("tag_name", ucfirst("history"))->first();
        $regimens = taskBank::where('id', '<>', $tagObject->id)->get();
        $recommendedRegimenIds = array_column($questionAnswerObject->recommendedRegimens->toArray(), "task_id");
        return [
            "data" => [
                "regimens" => $regimens,
                "recommended_task" => $recommendedRegimenIds
            ]
        ];
    }

    public function fetchTestRecommendAnswerInfo($questionId, $answerId)
    {
        $shaObject = ShortHealthAssessment::where('id', $questionId)->first();
        $questionAnswerObject = $shaObject->answers->where("id", $answerId)->first();
        $tests = LabsTest::all();
        $recommendedTestIds = array_column($questionAnswerObject->recommendedTests->toArray(), "test_id");
        return [
            "data" => [
                "tests" => $tests,
                "recommended_test" => $recommendedTestIds
            ]
        ];
    }

    public function showPage()
    {
        return view("admin.shortassess.index");
    }

    public function updateQuestionObj(Request $request, $id)
    {
        $validator = Validator::make($request->get('item'), [
            'question' => 'required',
            'answers' => 'required'
        ]);
        if ($validator->fails())
            return Helpers::sendResponse(["error" => $validator->getMessageBag()->first()]);
        try {
            $status = $this->shaServiceObj->updateQuestionObj($request->get('item'), $id);
            if ($status)
                return Helpers::sendResponse(["data" => "Question Updated"]);
            else
                return Helpers::sendResponse(["error" => "Answer Score Mapping failed, Please make sure  each answer has score"]);
        } catch (\Exception $e) {
            return Helpers::sendResponse(["error" => $e->getMessage()]);
        }
    }

    public function deleteQuestion($id)
    {
        try {
            $this->shaServiceObj->deleteQuestion($id);
            return Helpers::sendResponse(["data" => "Question deleted"]);
        } catch (\Exception $e) {
            return Helpers::sendResponse(["error" => $e->getMessage()]);
        }
    }

    public function insertQuestion(Request $request)
    {
        $items = $request->get('item');
        $validator = Validator::make($items, [
            'answers' => 'required',
            'header' => 'required',
            'multiple' => 'required',
            'question' => 'required'
        ]);
        if ($validator->fails())
            return Helpers::sendResponse(["error" => $validator->getMessageBag()->first()]);
        try {
            $this->shaServiceObj->insertQuestion($items);
            return Helpers::sendResponse(["data" => "New Question Inserted"]);
        } catch (\Exception $e) {
            return Helpers::sendResponse(["error" => $e->getMessage()]);
        }
    }

    /*
     *  Level Restriction Page and Logic
     */
    public function restrictionLevelPage()
    {
        return view("admin.shortassess.level-restriction");
    }

    public function restrictionLevelData()
    {
        $possibleAnswersOfSHA = SHAQuestionAnswers::all()->except(["created_at", "updated_at"]);
        $responseArr = [];
        foreach ($possibleAnswersOfSHA as $value) {
            if ($value->belongToQuestion->is_scoreable) {
                $responseArr[] = [
                    "id" => $value->id,
                    "answer" => $value->answer,
                    "question" => $value->belongToQuestion->question,
                    "restriction_level" => $value->restriction !== null ? $value->restriction->restriction_level : -1
                ];
            }
        }
        return $responseArr;
    }

    public function updateRestrictionLevelData($id, Request $request)
    {
        $validator = Validator::make($request->get("item"), [
            "restriction_level" => "required|numeric|min:0"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        $item = $request->get("item");
        $questionAnswerObj = SHAQuestionAnswers::where("id", $id)->first();
        try {
            $questionAnswerObj->restriction()->updateOrCreate([
                "sha_answer_id" => $id
            ], [
                "restriction_level" => $item["restriction_level"]
            ]);
            return ["data" => "Level  restriction updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function deleteRestrictionLevelData($id)
    {
        try {
            $questionObject = SHAQuestionAnswers::where("id", $id)->first();
            $questionObject->restriction()->delete();
            return ["data" => "Restriction Level For Answer Removed"];
        } catch (\Exception $exception) {
            return ["error" => $exception->getMessage()];
        }
    }
}
