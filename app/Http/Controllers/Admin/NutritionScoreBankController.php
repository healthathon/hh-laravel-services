<?php

namespace App\Http\Controllers\Admin;

use App\Model\Tasks\taskBank;
use App\Respositories\TaskBankRepository;
use App\Services\NutritionScoreBankService;
use App\Services\TaskServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NutritionScoreBankController extends Controller
{
    private $date, $nutritionScoreBankService, $taskBankRepo;

    public function __construct(NutritionScoreBankService $nutritionScoreBankService)
    {
        $this->date = date("y-m-d h:i:s");
        $this->nutritionScoreBankService = $nutritionScoreBankService;
        $this->taskBankRepo = new TaskBankRepository();
    }

    public function getPage()
    {
        return view("admin.ntr_bank.index");
    }

    public function getRecommendTask($id)
    {
        $nutritionBank = $this->nutritionScoreBankService->getScoreBankById($id);
        $recommendedTaskIds = $nutritionBank->recommendation == null ? [] : array_column($nutritionBank->recommendation->toArray(), "regimen_id");
        $data = [
            "expression" => $nutritionBank->expression,
            "id" => $nutritionBank->id,
            "score" => $nutritionBank->score
        ];
        $regimens = $this->taskBankRepo->getNutritionRegimens();
        return view("admin.ntr_bank.update-recommendation", compact("data", "recommendedTaskIds", "regimens"));
    }

    public function getAll()
    {
        try {
            $scoreBankData = $this->nutritionScoreBankService->getAllScoreBankData();
            $data = [];
            foreach ($scoreBankData as $bankDatum) {
                $data[] = [
                    'id' => $bankDatum->id,
                    'expression' => $bankDatum->expression,
                    'score' => $bankDatum->score
                ];
            }
            return $data;
        } catch (\Exception $e) {
            Log::info("Exception Occurred during fetching all score bank data " . $e->getMessage());
            return [
                "data" => $e->getMessage()
            ];
        }
    }

    public function updateScoreBank($id, Request $request)
    {
        $item = $request->get("item");
        $validator = Validator::make($item, [
            "score" => "required|min:0"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        try {
            $this->nutritionScoreBankService->updateScoreBank($id, $item);
            return ["data" => "Updated"];
        } catch (\Exception $exception) {
            return ["error" => $exception->getMessage()];
        }
    }

    public function insertRecommendRegimen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nutrition_bank_id" => "required",
            "recommended_task" => "required",
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        try {
            $this->nutritionScoreBankService->insertRecommendation($request->all());
            return ["data" => "Regimen Recommendation Updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
