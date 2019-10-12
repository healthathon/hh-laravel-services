<?php

use Illuminate\Database\Seeder;

class AssessmentAnswersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\AssessmentAnswers::truncate();
        $queries = \App\Model\Assess\Query::all();
        $massInsertData = [];
        foreach ($queries as $query) {
            $possibleAnswers = explode(",", $query->results_string);
            $scores = explode(",", $query->results_value);
            for ($i = 0; $i < count($possibleAnswers); $i++) {
                $massInsertData[] = [
                    'query_id' => $query->id,
                    'tag_id' => $query->tag_id,
                    'answer' => trim($possibleAnswers[$i]),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
            }
        }
        \App\Model\AssessmentAnswers::insert($massInsertData);
    }
}
