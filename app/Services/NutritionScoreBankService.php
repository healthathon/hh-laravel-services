<?php

namespace App\Services;


use App\Respositories\NutritionScoreBankRepository;
use App\Services\Interfaces\INutritionScoreBankService;
use Illuminate\Support\Facades\DB;

class NutritionScoreBankService implements INutritionScoreBankService
{

    private $nutritionBankRepo;

    public function __construct(NutritionScoreBankRepository $nutritionBankRepo = null)
    {
        $this->nutritionBankRepo = $nutritionBankRepo === null ? new NutritionScoreBankRepository() : $nutritionBankRepo;
    }

    function insertScoreBank(array $request)
    {
        return $this->nutritionBankRepo->insert($request);
    }

    function getAllScoreBankData()
    {
        return $this->nutritionBankRepo->fetchAll();
    }

    function getScoreBankById(int $id)
    {
        return $this->nutritionBankRepo->fetchScoreBankDataById($id);
    }

    function updateScoreBank(int $id, array $request)
    {
        return $this->nutritionBankRepo->update($id, $request);
    }

    /**
     * @param array $ntrBankData
     * @throws \Exception
     */
    public function insertRecommendation(array $ntrBankData)
    {
        $nutritionBankScoreObj = $this->nutritionBankRepo->fetchScoreBankDataById($ntrBankData["nutrition_bank_id"]);
        try {
            DB::beginTransaction();
            $nutritionBankScoreObj->recommendation()->delete();
            $newData = [];
            foreach ($ntrBankData["recommended_task"] as $ids) {
                $newData[] = [
                    "regimen_id" => $ids,
                    "nutrition_bank_id" => $ntrBankData["nutrition_bank_id"],
                    "updated_at" => date("y-m-d h:i:s"),
                    "created_at" => date("y-m-d h:i:s")
                ];
            }
            $nutritionBankScoreObj->recommendation()->insert($newData);
            DB::commit();
            return;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    function getRecommendedRegimenIds(int $tagScore)
    {
        $regimensIds = [];
        $nutritionBankScore = $this->nutritionBankRepo->fetchAll();
        foreach ($nutritionBankScore as $value) {
            $expression = $tagScore . " " . $value->expression . " " . $value->score;
            if (eval("return " . $expression . ";")) {
                $regimensIds = $value->recommendation == null ?
                    [] : array_column($value->recommendation->toArray(), "regimen_id");
                break;
            }
        }
        return $regimensIds;
    }
}