<?php

use Illuminate\Database\Seeder;

class AssessmentAnswersCorrespondingTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $queries = \App\Model\Assess\Query::all();
        $massInsertData = [];
        foreach ($queries as $query) {
            $possibleAnswers = explode(",", $query->results_string);
            foreach ($possibleAnswers as $possibleAnswer) {
                $massInsertData[] = [
                    'query_id' => $query->id,
                    'tag_id' => $query->tag_id,
                    'answer' => $possibleAnswer,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                    'recommended_test' => null
                ];
            }
        }
        \App\Model\TestCorrespondingAssessmentQuestionsAnswer::insert($massInsertData);
    }
}
