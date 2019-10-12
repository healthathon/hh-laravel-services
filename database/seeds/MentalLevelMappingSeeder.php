<?php

use Illuminate\Database\Seeder;

class MentalLevelMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mentalMappingLevel = new \App\Model\MentalWellBeingLevelMapping();
        $data = [
            [
                "score" => 12,
                "tag" => "MH4",
                "level" => 4,
                "state" => "good",
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s"),
            ],
            [
                "score" => 6,
                "tag" => "MH3",
                "level" => 3,
                "state" => "slight problem",
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s"),
            ],
            [
                "score" => 4,
                "tag" => "MH2",
                "level" => 2,
                "state" => "depressed",
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s"),
            ],
            [
                "score" => 0,
                "tag" => "MH1",
                "level" => 0,
                "state" => "intervention",
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s"),
            ],
        ];
        $mentalMappingLevel->insert($data);
    }
}
