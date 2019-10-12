<?php

namespace App\Respositories;


use App\Model\Assess\queryTag;
use App\Model\SHAQuestionAnswers;
use App\Model\ShortHealthAssessment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use phpDocumentor\GraphViz\Exception;

class SHARepository
{

    function fetchAllQuestions()
    {
        return ShortHealthAssessment::with('answers:id,question_id,answer,score')->get();
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    function updateQuestion(int $id, array $data)
    {
        $answers = $data["answers"];
        $scores = $data["score"];
        $multipleAnswers = explode(",", $answers);
        $multipleScores = explode(",", $scores);
        if (count($multipleAnswers) !== count($multipleScores))
            return false;
        $data = Arr::except($data, ["answers", "score"]);
        $questionObj = ShortHealthAssessment::where('id', $id)->first();
        $existingAnswers = array_column($questionObj->answers->toArray(), 'answer');
        // Unnecessary code complexity (Reason: Time Constraints)
        $deletedValues = array_diff($existingAnswers, $multipleAnswers);
        DB::beginTransaction();
        DB::transaction(function () use ($multipleAnswers, $multipleScores, $deletedValues, $id, $data) {
            try {
                for ($i = 0; $i < count($multipleAnswers); $i++) {
                    SHAQuestionAnswers::updateOrCreate([
                        'question_id' => $id,
                        'answer' => $multipleAnswers[$i],
                    ], [
                        'question_id' => $id,
                        'score' => $multipleScores[$i],
                        'answer' => $multipleAnswers[$i]
                    ]);
                }
                if (count($deletedValues) > 0) {
                    foreach ($deletedValues as $deletedValue) {
                        SHAQuestionAnswers::where('question_id', $id)
                            ->where('answer', $deletedValue)
                            ->delete();
                    }
                }
                ShortHealthAssessment::where('id', $id)->update($data);
                $this->updateHistoryOverallScore();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw  new Exception($e);
            }
        });
        return true;
    }

    // HardCoded
    function updateHistoryOverallScore()
    {
        $historyObject = queryTag::where("tag_name", ucfirst("history"))->first();
        $shortHealthAssessObj = ShortHealthAssessment::all();
        $overallScore = 0;
        foreach ($shortHealthAssessObj as $value) {
            if ($value->is_scoreable && $value->multiple) {
                $overallScore += $value->answers()->count();
            }
            if ($value->is_scoreable && !$value->multiple) {
                $overallScore += max($value->answers()->pluck("score")->toArray());
            }
        }
        $historyObject->overallScore->score = $overallScore;
        $historyObject->overallScore->save();
    }

    function deleteQuestion(int $id)
    {
        return ShortHealthAssessment::where('id', $id)->with('answers:id')->delete();
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function insertQuestion(array $data)
    {
        $answers = $data["answers"];
        unset($data["answers"]);
        $multipleAnswers = explode(",", $answers);
        $answersArr = [];
        DB::beginTransaction();
        DB::transaction(function () use ($data, $multipleAnswers, $answersArr) {
            try {
                $object = ShortHealthAssessment::create($data);
                foreach ($multipleAnswers as $multipleAnswer) {
                    $answersArr[] = [
                        'question_id' => $object->id,
                        'answer' => $multipleAnswer
                    ];
                }
                SHAQuestionAnswers::insert($answersArr);
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                return false;
            }
        });
    }
}