<?php

namespace App\Respositories;

use App\Model\BmiScore;
use App\Model\UserAchievements;
use Illuminate\Database\Eloquent\Model;

class UserAchievementsRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new UserAchievements());
    }

}