<?php

namespace App\Respositories;


use App\Model\DiagnosticLabInformation;

class DiagnosticLabInformationRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new DiagnosticLabInformation());
    }

}