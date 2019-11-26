<?php

namespace App\Respositories;

use App\Model\MentalWellBeingLevelMapping;

class MentalWellBeingLevelMappingRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new MentalWellBeingLevelMapping());
    }

}