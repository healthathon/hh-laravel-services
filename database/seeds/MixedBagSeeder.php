<?php

use Illuminate\Database\Seeder;

class MixedBagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regimenNames = [
            "Mixed Bag 1",
            "Mixed Bag 2",
            "Mixed Bag 3",
            "Mixed Bag 4",
        ];
        $regimenCategoryMapper = [
            "Mixed Bag 1" => 2,
            "Mixed Bag 2" => 3,
            "Mixed Bag 3" => 1,
            "Mixed Bag 4" => 4
        ];
        $regimenTasksFor7Days = [
            "Mixed Bag 1" => [
                "5 min warm up, 5 mins brisk walking, 5 mins cool down",
                "5 min warm up, 5 mins brisk walking, 5 mins cool down",
                "Take stock: Calculate how many spoons of sugar you consume in your tea/coffee today",
                "Don't add sugar to your evening cup of tea/coffee",
                "Take stock: Observe your mind for 5 minutes, note how many times in 5 minutes it gets distracted and starts thinking wasteful thoughts",
                "For 5 mins bring awareness to any sensation in your body - how fast is your heart beating/are your hands warm or cold/ is your breathing shallow or fast etc",
                "Rest"
            ],
            "Mixed Bag 2" => [
                "Walk to vegetable market/grocery store",
                "Take stock: Roughly calculate how many servings of fruit you have today. 1 servings = 100 gms",
                "Take stock:Note down how much time you spend around nature - in a park, with your plants, getting sunshine, by a lake etc",
                "Take stock: Make a note of how many hours you sleep at night",
                "Walk to vegetable market/grocery store",
                "Have one fruit before breakfast",
                "Rest"
            ],
            "Mixed Bag 3" => [
                "5 min warm up, 5 mins brisk walking, 5 mins cool down",
                "5 min warm up, 5 mins brisk walking, 5 mins cool down",
                "Walk to vegetable market/grocery store",
                "Take stock: Observe your mind for 5 minutes, note how many times in 5 minutes it gets distracted and starts thinking wasteful thoughts",
                "Get sunshine for 15 mins early morning before 8 am",
                "Take stock: Calculate how many spoons of sugar you consume in your tea/coffee today",
                "Rest"
            ],
            "Mixed Bag 4" => [
                "5 min warm up, 5 mins brisk walking, 5 mins cool down",
                "Take stock: Make a note of how many hours you sleep at night",
                "Walk to vegetable market/grocery store",
                "For 5 mins bring awareness to any sensation in your body - how fast is your heart beating/are your hands warm or cold/ is your breathing shallow or fast etc",
                "Take stock:Note down how much time you spend around nature - in a park, with your plants, getting sunshine, by a lake etc",
                "Take stock: Make a note of the time you go to bed every night",
                "Rest"
            ]
        ];

        foreach ($regimenNames as $regimenName) {
            $mixedBag = new \App\Model\MixedBag();
            $mixedBag->regimen_name = $regimenName;
            $mixedBag->mapper = $regimenCategoryMapper[$regimenName];
            $sevenDayTaskForCurrentMixedBag = $regimenTasksFor7Days[$regimenName];
            $mixedBag->day_1 = $sevenDayTaskForCurrentMixedBag[0];
            $mixedBag->day_2 = $sevenDayTaskForCurrentMixedBag[1];
            $mixedBag->day_3 = $sevenDayTaskForCurrentMixedBag[2];
            $mixedBag->day_4 = $sevenDayTaskForCurrentMixedBag[3];
            $mixedBag->day_5 = $sevenDayTaskForCurrentMixedBag[4];
            $mixedBag->day_6 = $sevenDayTaskForCurrentMixedBag[5];
            $mixedBag->day_7 = $sevenDayTaskForCurrentMixedBag[6];
            $mixedBag->user_ids = "";
            $mixedBag->save();
        }
    }
}
