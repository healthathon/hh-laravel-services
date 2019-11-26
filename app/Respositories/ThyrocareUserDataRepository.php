<?php

namespace App\Respositories;

use App\Model\ThyrocareUserData;


class ThyrocareUserDataRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new ThyrocareUserData());
    }
}