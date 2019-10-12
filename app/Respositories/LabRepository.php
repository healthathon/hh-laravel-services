<?php

namespace App\Respositories;


use App\Model\LabsTest;

class LabRepository
{

    public function __construct()
    {
    }

    public function fetchTestNamesFromIds($commaSeparatedTestId)
    {
        return LabsTest::whereIn('id', $commaSeparatedTestId)->select('test_name', 'price')->get();
    }
}