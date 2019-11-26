<?php

namespace App\Respositories;

use App\Model\MMGBookingMailInfo;

class MMGBookingMailInfoRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new MMGBookingMailInfo());
    }
}