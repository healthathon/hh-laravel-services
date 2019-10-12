<?php

namespace App\Services;


use App\Respositories\LabRepository;
use App\Services\Interfaces\ILabService;

class LabService implements ILabService
{
    private $labRepo;

    public function __construct()
    {
        $this->labRepo = new LabRepository();
    }

    function getMultipleTestNameFromTestIds($commaSeparatedTestId)
    {
        return $this->labRepo->fetchTestNamesFromIds($commaSeparatedTestId);
    }
}