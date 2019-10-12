<?php

use Illuminate\Database\Seeder;

class ShortHealthAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shortAssessment = new \App\Model\ShortHealthAssessment();
        $shortAssessment->header = "Goals";
        $shortAssessment->question = "What are your goals?";
        $shortAssessment->answers = "Get Fitter,Eat Healthier,Feel Happier,Assess Health,Track Health";
        $shortAssessment->multiple = true;
        $shortAssessment->save();
    }
}
