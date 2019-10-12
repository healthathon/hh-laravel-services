<?php

use Illuminate\Database\Seeder;

class TagTotalScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\TagTotalScore::truncate();
        $tags = \App\Model\Assess\Query::all()->groupBy('tag_id');
        $info = [];
        foreach ($tags as $key => $tag) {
            $score = 0;
            foreach ($tag as $query) {
                $values = explode(",", $query->results_value);
                $score += max($values);
            }
            $info[] = [
                'tag_id' => $key,
                'score' => $score,
                'created_at' => date("Y-m-d h:i:s"),
                'updated_at' => date("Y-m-d h:i:s")
            ];
        }
        \App\Model\TagTotalScore::insert($info);
    }
}
