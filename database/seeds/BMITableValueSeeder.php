<?php

use Illuminate\Database\Seeder;

class BMITableValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bmiValues = [
            [
                'deviation_range' => 'val==0',
                'score' => 6
            ],
            [
                'deviation_range' => 'val<=10',
                'score' => 4
            ],
            [
                'deviation_range' => 'val>10 && val<=20',
                'score' => 2
            ]
        ];
        \App\Model\BmiScore::insert($bmiValues);
    }
}
