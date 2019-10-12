<?php

use Illuminate\Database\Seeder;

class DiagnosticLabInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $diagnosticLabModelObj = new \App\Model\DiagnosticLabInformation();
        $diagnosticLabModelObj->name = "Thyrocare";
        $diagnosticLabModelObj->description = "A Diagnostic Lab";
        $diagnosticLabModelObj->save();
    }
}
