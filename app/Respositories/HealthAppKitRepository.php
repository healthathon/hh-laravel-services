<?php

namespace App\Respositories;
use App\Model\HealthAppKit;

class HealthAppKitRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new HealthAppKit());
    }
}