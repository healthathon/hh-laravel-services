<?php

use Illuminate\Database\Seeder;

class NutritionScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nutritionSeeder = new \App\Model\NutritionScoreBank();
        $data = [
            [
                "expression" => "<=",
                "score" => 20,
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s"),
            ],
            [
                "expression" => ">",
                "score" => 20,
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s"),
            ]
        ];
        $nutritionSeeder->insert($data);
    }
}
