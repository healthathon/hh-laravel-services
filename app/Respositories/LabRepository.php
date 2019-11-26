<?php

namespace App\Respositories;


use App\Model\LabsTest;

class LabRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new LabsTest());
    }

    public function fetchTestNamesFromIds($commaSeparatedTestId)
    {
        return $this->model->whereIn('id', $commaSeparatedTestId)->select('test_name', 'price')->get();
    }

    public function deleteTestById(int $id)
    {
        return $this->model->where('id', $id)->delete();
    }
}