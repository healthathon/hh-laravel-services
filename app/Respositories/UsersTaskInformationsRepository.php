<?php

namespace App\Respositories;


use App\Exceptions\BlogNotFoundException;
use App\Model\UsersTaskInformations;

class UsersTaskInformationRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new UsersTaskInformations());
    }
}