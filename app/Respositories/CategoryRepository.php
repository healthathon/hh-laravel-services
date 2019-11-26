<?php

namespace App\Respositories;

use App\Exceptions\CategoryNotFoundException;
use App\Model\Category;

class CategoryRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new Category());
    }

    /**
     * @param $name
     * @return mixed
     * @throws CategoryNotFoundException
     */
    public function getCategoryIdByName($name)
    {
        $name = strtolower($name);
        $categoryObj = $this->model->where('name', ucfirst($name))->first();
        if ($categoryObj == null)
            throw new CategoryNotFoundException();
        return $categoryObj->id;
    }

    /**
     * This function returns category id based on category name
     * @param $categoryName :  The Name of Category
     * @return mixed
     */
    public function getCategoryInfo($categoryName)
    {
        return $this->model->where('name', ucfirst($categoryName))->first(['id', 'name']);
    }
}