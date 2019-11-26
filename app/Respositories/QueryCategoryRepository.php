<?php

namespace App\Respositories;


use App\Model\Assess\queryCategory;

class QueryCategoryRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new queryCategory());
    }

}