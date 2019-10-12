<?php

namespace App\Http\Controllers\Admin;

use App\Constants;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Assess\AssessmentQuestionsTagOrder;
use App\Model\Assess\Query;
use App\Model\Assess\queryCategory;
use App\Model\Assess\queryTag;
use App\Model\Assess\scoreInterp;
use App\Model\AssessmentAnswers;
use App\Model\Category;
use App\Model\LabsTest;
use App\Model\MentalWellBeingLevelMapping;
use App\Model\Tasks\taskBank;
use App\Model\TestCorrespondingAssessmentQuestionsAnswer;
use App\Respositories\CategoryRepository;
use App\Services\AssessmentService;
use App\Services\LabService;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssessmentController extends Controller
{
    private $assessmentService, $labService, $questionService, $categoryRepo;

    public function __construct()
    {
        $this->assessmentService = new AssessmentService();
        $this->labService = new LabService();
        $this->questionService = new QuestionService();
        $this->categoryRepo = new CategoryRepository();
    }

    public function taskRecommendRegimenPage()
    {
        return view('admin.tasks.recommendRegimen');
    }

    public function fetchRecommendRegimen()
    {
        return $this->assessmentService->fetchAllAssessAnswers();
    }

    public function updateRestrictionLevel(Request $request, int $id)
    {
        $item = $request->get("item");
        $validator = Validator::make($request->get("item"), [
            "restricted_level" => "required|min:0"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        try {
            AssessmentAnswers::where("id", $id)->update(["restricted_level" => $item["restricted_level"]]);
            return ["data" => "Recommendation Updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function getQueryAnswersRegimenPage($queryId, $answer)
    {
        $query = $this->questionService->fetchQuestionById($queryId, ['id', 'tag_id', 'query', 'results_string']);
        $answerObj = $query->answers()->where('answer', $answer)->first();
        $answers = explode(",", $query->results_string);
        $recommendedIds = array_column($answerObj->recommend_regimen->toArray(), 'recommended_regimen');
        try {
            $categoryId = $this->categoryRepo->getCategoryIdByName(Constants::PHYSICAL);
            $regimens = taskBank::where('category', '<>', $categoryId)->get(['id', 'task_name', 'title']);
            return view('admin.tasks.modifyRegimen', compact('query', 'answers',
                'regimens', 'recommendedIds'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function modifyQueryAnswersRegimen(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'answer' => 'required',
            'query_id' => 'required'
        ]);
        if ($validation->fails())
            return Helpers::sendResponse(["error" => $validation->getMessageBag()->first()]);
        $answer = $request->get('answer');
        $queryId = $request->get('query_id');
        $regimenIds = $request->get('recommended_regimen');
        $regimenIds = is_array($regimenIds) ? $regimenIds : [];
        return $this->assessmentService->updateQueryAnswersRegimen($answer, $queryId, $regimenIds);
    }

    public function getRegimensCorrespondingToAnswer($queryId, $answer)
    {
        $queryAnswerObject = AssessmentAnswers::where('query_id', $queryId)
            ->where('answer', $answer)->first();
        $recommendedIds = array_column($queryAnswerObject->recommend_regimen->toArray(), 'recommended_regimen');
        $categoryId = Category::where('name', ucfirst(Constants::PHYSICAL))->first()->id;
        // Do not send physical task
        $regimens = taskBank::where('category', '<>', $categoryId)->get(['id', 'task_name', 'title']);
        return [
            'status' => true,
            'recommended_task' => $recommendedIds,
            'regimens' => $regimens
        ];
    }

    public function testRecommendationPage()
    {
        return view('admin.assess.testcorresponsdinganswers');
    }

    public function fetchRecommendedTests()
    {
        $assessmentAnswers = AssessmentAnswers::all();
        $customizeDataArr = [];
        foreach ($assessmentAnswers as $answer) {
            $testIds = array_column($answer->recommend_test->toArray(), 'recommended_test');
            $recommendedTestNames = null;
            if (count($testIds) > 0) {
                $recommendedTestNames = $this->assessmentService->mapTestNameWithTestIds($testIds);
                $recommendedTestNames = count($recommendedTestNames) > 0 ? implode(",", $recommendedTestNames) : null;
            }
            $customizeDataArr[] = [
                'query' => $answer->queryInfo->query,
                'query_id' => $answer->queryInfo->id,
                'recommended_test' => $recommendedTestNames,
                'answer' => $answer->answer,
            ];
        }
        return $customizeDataArr;
    }

    public function updateTestRecommendationPage($queryId, $answer)
    {
        $query = Query::where('id', $queryId)->first(['id', 'tag_id', 'query', 'results_string']);
        $queryAnswerObject = AssessmentAnswers::where('query_id', $queryId)
            ->where('answer', $answer)->first();
        $answers = explode(",", $query->results_string);
        // RecommendedTest : Test recommend to corresponding answers
        $recommendedIds = array_column($queryAnswerObject->recommend_test->toArray(), 'recommended_test');
        $assessmentTests = LabsTest::all();
        return view('admin.assess.modifyTest', compact('query', 'answers',
            'assessmentTests', 'recommendedIds'));
    }

    public function getTestsCorrespondingToAnswer($queryId, $answer)
    {
        $queryAnswerObject = AssessmentAnswers::where('query_id', $queryId)
            ->where('answer', $answer)->first();
        $recommendedIds = array_column($queryAnswerObject->recommend_test->toArray(), 'recommended_test');
        $tests = LabsTest::all()->toArray();
        return [
            'status' => true,
            'recommended_test' => $recommendedIds,
            'tests' => $tests
        ];
    }

    public function updateTestRecommendations(Request $request)
    {
        $answer = $request->get('answer');
        $queryId = $request->get('query_id');
        $testIds = $request->get('recommended_test');
        $testIds = is_array($testIds) ? $testIds : [];
        $answerId = AssessmentAnswers::where('query_id', $queryId)
            ->where('answer', $answer)
            ->first()->id;
        $newRecommendedTest = [];
        foreach ($testIds as $testId) {
            $newRecommendedTest[] = [
                'answer_id' => $answerId,
                'recommended_test' => $testId
            ];
        }
        $previousRecommendation = TestCorrespondingAssessmentQuestionsAnswer::where('answer_id', $answerId);
        if (count($previousRecommendation->get()) > 0) {
            if ($previousRecommendation->delete()) {
                return $this->updateTestRecommendation($newRecommendedTest);
            } else {
                return response()->json([
                    'status' => false
                ]);
            }
        } else {
            return $this->updateTestRecommendation($newRecommendedTest);
        }
    }

    private function updateTestRecommendation($newRecommendedRegimen)
    {
        TestCorrespondingAssessmentQuestionsAnswer::insert($newRecommendedRegimen);
        return response()->json([
            'status' => true,
            'message' => "Updated"
        ]);
    }

    public function rearrangeAssessmentTags()
    {
        $tags = queryTag::getTagsNameWithId();
        return view('admin.assess.assessmentTagOrder', compact('tags'));
    }

    public function getDefinedTagOrderSequence()
    {
        $tagNames = [];
        $orderSequences = AssessmentQuestionsTagOrder::all()->toArray();
        foreach ($orderSequences as $orderSequence) {
            $nameArr = [];
            $idsArr = [];
            $ids = explode(",", $orderSequence['order_seq']);
            foreach ($ids as $id) {
                $nameArr[] = queryTag::getTagNameFromCache($id);
                $idsArr[] = $id;
            }
            $tagNames[] = [
                'id' => $orderSequence['id'],
                'sequence' => [
                    'tags' => implode(",", $nameArr),
                    'ids' => implode(",", $idsArr),
                ],
                'is_active' => $orderSequence['is_active']
            ];
        }
        return $tagNames;
    }

    public function updateTagOrder(Request $request)
    {
        $item = $request->get("item");
        try {
            DB::table('assessment_questions_tag_orders')->update(['is_active' => 0]);
            AssessmentQuestionsTagOrder::where('id', $item["id"])->update(['is_active' => 1]);
            return ["data" => "Updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    // ------------------------------    Following Old Structure Code -----------------------------
//    <----------------Category Operation Part --------------------------->

    public function postNewQuestionsTagOrder(Request $request)
    {
        $tagIds = $request->get('tagIds');
        $status = $request->get('status');
        $totalTagsExceptBMI = queryTag::where('tag_name', '<>', 'BMI')
            ->where('tag_name', '<>', 'History')
            ->count();
        if (count($tagIds) !== $totalTagsExceptBMI)
            return ["error" => "Please select all tags"];
        else {
            $newTagOrder = new AssessmentQuestionsTagOrder();
            $newTagOrder->order_seq = implode(",", $tagIds);
            $newTagOrder->is_active = $status;
            $newTagOrder->save();
            if ($status)
                // Deactivate all other order sequence
                AssessmentQuestionsTagOrder::where('id', '<>', $newTagOrder->id)->update(['is_active' => 0]);
            return ["data" => "New Tag Order Saved"];
        }
    }

    public function showQuestionCategory()
    {
        return view('admin.assess.category');
    }

    public function showQuestionCategoryList()
    {
        $categories = queryCategory::all();
        $result = Array();
        $i = 0;
        foreach ($categories as $category) {
            $result[$i]['Category_Name'] = $category->category_name;
            $result[$i]['Happy_Zone'] = $category->happy_marks;
            $result[$i]['Excellent'] = $category->excellent_marks;
            $result[$i]['Good'] = $category->good_marks;
            $result[$i]['Bad'] = $category->bad_marks;
            $result[$i]['ID'] = $category->id;
            $i++;
        }
        return response($result)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }


//    <------------------Tag Operation Part------------------------------->

    public function update_category(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $category = queryCategory::find($id);
        $category->category_name = $item['Category_Name'];
        $category->happy_marks = $item['Happy_Zone'];
        $category->excellent_marks = $item['Excellent'];
        $category->good_marks = $item['Good'];
        $category->bad_marks = $item['Bad'];
        $category->save();
    }

    public function showQuestionTag()
    {
        $categories = queryCategory::all();
        $i = 0;
        $result = Array();
        foreach ($categories as $category) {
            $result[$i]['category_name'] = $category->category_name;
            $result[$i]['category_id'] = $category->id;
            $i++;
        }
        $result = json_encode($result);
        return view('admin.assess.tag', compact('result'));
    }

    public function showQuestionTagList()
    {
        $tags = queryTag::all();
        $result = Array();
        $i = 0;
        foreach ($tags as $tag) {
            $result[$i]['Tag_Name'] = $tag->tag_name;
            $result[$i]['Happy_Zone'] = $tag->happy_marks;
            $result[$i]['Work_More'] = $tag->work_more_score;
            $result[$i]['Excellent'] = $tag->excellent_marks;
            $result[$i]['Good'] = $tag->good_marks;
            $result[$i]['Bad'] = $tag->bad_marks;
            $result[$i]['ID'] = $tag->id;
            $result[$i]['Category'] = $tag->category_id;
            $i++;
        }
        return response($result)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }


//    < ---------------Question Part ---------------------------- >

    public function update_tag(Request $request)
    {

        $item = $request->input('item');
        $id = $item['ID'];
        $tag = queryTag::find($id);
        $tag->tag_name = $item['Tag_Name'];
        $tag->happy_marks = $item['Happy_Zone'];
        $tag->work_more_score = $item['Work_More'];
        $tag->excellent_marks = $item['Excellent'];
        $tag->good_marks = $item['Good'];
        $tag->bad_marks = $item['Bad'];
        $tag->category_id = $item['Category'];
        $tag->save();
    }

    public function showQuestion()
    {
        $tags = queryTag::all();
        $result = Array();
        $i = 0;
        foreach ($tags as $tag) {
            $result[$i]['tag_id'] = $tag->id;
            $result[$i]['tag_name'] = $tag->tag_name;
            $i++;
        }
        $result = json_encode($result);
        return view('admin.assess.question', compact('result'));
    }

    public function showQuestionList()
    {
        $queries = Query::all();
        $result = Array();
        $i = 0;
        foreach ($queries as $query) {
            $result[$i]['Tag'] = $query->tag_id;
            $result[$i]['Query'] = $query->query;
            $result[$i]['Result_String'] = $query->results_string;
            $result[$i]['Result_Mark'] = $query->results_value;
            $result[$i]['restricted_level'] = join(",", array_column($query->answers->toArray(), "restricted_level"));
            $result[$i]['ID'] = $query->id;
            $result[$i]['is_mental_bank'] = $query->is_mental_bank;
            $i++;
        }
        return response($result)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function update_question(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $query = Query::find($id);
        $query->tag_id = $item['Tag'];
        $query->query = $item['Query'];
        $query->results_string = $item['Result_String'];
        $query->results_value = $item['Result_Mark'];
        $query->is_mental_bank = $item['is_mental_bank'];
        DB::beginTransaction();
        try {
            $query->answers()->delete();
            $updatedAnswers = explode(",", $item["Result_String"]);
            $restrictedLevel = explode(",", $item["restricted_level"]);
            $answerObject = [];
            for ($i = 0; $i < count($updatedAnswers); $i++) {
                $answerObject[] = [
                    "query_id" => $query->id,
                    "tag_id" => $query->tag_id,
                    "answer" => trim($updatedAnswers[$i]),
                    "restricted_level" => $restrictedLevel[$i] == "" ? null : (int)$restrictedLevel[$i],
                    "created_at" => date("y-m-d h:i:s"),
                    "updated_at" => date("y-m-d h:i:s"),
                ];
            }
            $query->answers()->insert($answerObject);
            $query->save();
            Cache::forget("queries_mental_bank");
            DB::commit();
            return ['data' => "Question Updated"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function insert_question(Request $request)
    {
        $item = $request->input('item');
        try {
            DB::beginTransaction();
            $query = new Query;
            $query->tag_id = $item['Tag'];
            $query->query = $item['Query'];
            $query->results_string = $item['Result_String'];
            $query->results_value = $item['Result_Mark'];
            $query->save();
            $item['Tag'] = (int)$item['Tag'];
            $item['ID'] = $query->id;
            $possibleAnswers = explode(",", $item["Result_String"]);
            $newEntryInAssessmentAnswers = [];
            foreach ($possibleAnswers as $possibleAnswer) {
                $newEntryInAssessmentAnswers[] = [
                    'tag_id' => $item['Tag'],
                    'query_id' => $item['ID'],
                    'answer' => trim($possibleAnswer),
                    'created_at' => date("y-m-d h:i:s"),
                    'updated_at' => date("y-m-d h:i:s"),
                ];
            }
            $this->updateTagOverallScore($item['Tag'], $item['Result_Mark'], Constants::ADD);
            AssessmentAnswers::insert($newEntryInAssessmentAnswers);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception occurred while saving new assessment questions " . $e->getTraceAsString());
            return response($e->getMessage())
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ]);
        }
    }

    private function updateTagOverallScore($tagId, $resultsValue, $action = Constants::ADD)
    {
        $queryInfo = queryTag::where('id', $tagId)->first();
        $scores = explode(",", $resultsValue);
        switch ($action) {
            case Constants::ADD:
                $queryInfo->overallScore->score += max($scores);
                break;
            case Constants::REMOVE:
                $queryInfo->overallScore->score -= max($scores);
                break;
            case Constants::UPDATE:
                break;
        }
        $queryInfo->overallScore->save();
    }

//<--------------------- Interpolation Part ---------------------------------->

    public function delete_question(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $query = Query::find($id);
        $this->updateTagOverallScore($query->tag_id, $query->results_value, Constants::REMOVE);
        AssessmentAnswers::where('query_id', $query->id)->delete();
        $query->delete();
    }

    public function showInterp()
    {
        $result = Array();
        $categories = queryCategory::all();
        $i = 0;
        foreach ($categories as $category) {
            $result[$i]['category_name'] = $category->category_name;
            $i++;
        }
        $result = json_encode($result);
        return view('admin.assess.interp', compact('result'));
    }

    public function showInterpList()
    {
        $interps = scoreInterp::all();
        $result = Array();
        $i = 0;
        foreach ($interps as $interp) {
            $category1 = $interp->category1;
            $category2 = $interp->category2;
            $category3 = $interp->category3;
            $result[$i]['category1'] = $category1;
            $result[$i]['category2'] = $category2;
            $result[$i]['category3'] = $category3;
            $result[$i]['level'] = $interp->level;
            $result[$i]['ID'] = $interp->id;
            $i++;
        }
        return response($result)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function insert_interp(Request $request)
    {
        $item = $request->input('item');
        $interp = new scoreInterp;
        $interp->category1 = $item['category1'];
        $interp->category2 = $item['category2'];
        $interp->category3 = $item['category3'];
        $interp->level = $item['level'];
        $interp->save();
        $item['category1'] = (int)$item['category1'];
        $item['category2'] = (int)$item['category2'];
        $item['category3'] = (int)$item['category3'];
        $item['level'] = (int)$item['level'];

        $item['ID'] = $interp->id;
        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function update_interp(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $interp = scoreInterp::find($id);
        $interp->category1 = $item['category1'];
        $interp->category2 = $item['category2'];
        $interp->category3 = $item['category3'];
        $item['category1'] = (int)$item['category1'];
        $item['category2'] = (int)$item['category2'];
        $item['category3'] = (int)$item['category3'];
        $item['level'] = (int)$item['level'];
        $interp->level = $item['level'];
        $interp->save();
        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

//<--------------------- Interpolation Part End---------------------------------->

//----- Will Show All Test which application user will see to his corresponding answers.
//<--------------------- Assessment Test Part---------------------------------->

    public function delete_interp(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $interp = scoreInterp::find($id);
        $interp->delete();
    }

    public function viewTestsPage()
    {
        return view('admin.diagnosticLab.tabularTestView');
    }

    public function addOrUpdateTest(Request $request, $isEditPage)
    {
        $dataArray = $request["item"];
        if ($isEditPage) {
            LabsTest::where('id', $dataArray['id'])
                ->update(['test' => $dataArray['test']]);
        } else {
            $object = LabsTest::create($dataArray);
            $dataArray["id"] = $object->id;
        }
        return $dataArray;
    }

    public function deleteTest($id)
    {
        LabsTest::where('id', $id)->delete();
    }

    public function mentalScoreLevelMappingPage()
    {
        return view("admin.assess.mentalscorelevelmapping");
    }

    public function fetchMentalScoreLevelMappingInfo()
    {
        return MentalWellBeingLevelMapping::orderBy("score")->get();
    }

    public function insertMentalScoreLevelMappingInfo(Request $request)
    {
        $item = $request->get("item");
        $validator = Validator::make($item, [
            "score" => "required|numeric|min:0",
            "level" => "required|numeric|min:0",
            "state" => "required"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        try {
            MentalWellBeingLevelMapping::create($item);
            return ["data" => "New Score Level Mapping Added"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function updateMentalScoreLevelMappingInfo($id, Request $request)
    {
        $item = $request->get("item");
        $validator = Validator::make($item, [
            "score" => "required|numeric|min:0",
            "level" => "required|numeric|min:0",
            "state" => "required"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        try {
            $item = array_except($item, "id");
            MentalWellBeingLevelMapping::where("id", $id)->update($item);
            return ["data" => "Score Level Mapping Updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function deleteMentalScoreLevelMappingInfo($id)
    {
        try {
            MentalWellBeingLevelMapping::where("id", $id)->delete();
            return ["data" => "Score Level Mapping Deleted"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}


