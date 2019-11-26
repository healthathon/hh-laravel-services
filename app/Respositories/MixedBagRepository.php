<?php

namespace App\Respositories;

use App\Model\MixedBag;

class MixedBagRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new MixedBag());
    }

    /**
     * This function fetch the mixed bag task object from given ID
     *
     * @param $regimenId : Regimen ID
     * @return mixed: Object of Regimen
     */
    public function getObject($regimenId)
    {
        return $this->model->where('id', $regimenId)->first();
    }
}