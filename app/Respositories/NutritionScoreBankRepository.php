<?php

namespace App\Respositories;


use App\Model\NutritionScoreBank;

class NutritionScoreBankRepository
{

    public function insert(array $request)
    {
        return NutritionScoreBank::create($request);
    }

    public function fetchAll()
    {
        return NutritionScoreBank::all();
    }

    public function fetchScoreBankDataById(int $id)
    {
        return NutritionScoreBank::where("id", $id)->first();
    }

    public function update(int $id, array $request)
    {
        $request = array_except($request, "id");
        return NutritionScoreBank::where("id", $id)->update($request);
    }
}