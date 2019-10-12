<?php

use Illuminate\Database\Seeder;

// Seeder to add given test of MapMyGenome
class MapMyGenomeTestSeeder extends Seeder
{

    private $testData = [
        [
            "lab_id" => 2,
            "test_name" => "Genomepatri",
            "abbr" => "GPK",
            "about" => "You need to maintain a 30-minute fast prior to collecting your sample(absolute no water, smoke, food, etc)",
            "price" => 15000
        ],
        [
            "lab_id" => 2,
            "test_name" => "Myfitgene",
            "abbr" => "MFG",
            "about" => "You need to maintain a 30-minute fast prior to collecting your sample(absolute no water, smoke, food, etc)",
            "price" => 15000
        ],
        [
            "lab_id" => 2,
            "test_name" => "Medicamap",
            "abbr" => "MMP",
            "about" => "You need to maintain a 30-minute fast prior to collecting your sample(absolute no water, smoke, food, etc)",
            "price" => 15000
        ],
        [
            "lab_id" => 2,
            "test_name" => "Mynutrigene",
            "abbr" => "MNG",
            "about" => "You need to maintain a 30-minute fast prior to collecting your sample(absolute no water, smoke, food, etc)",
            "price" => 9999
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\LabsTest::insert($this->testData);
    }
}
