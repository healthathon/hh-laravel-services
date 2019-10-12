<?php

namespace App\Respositories;


use App\Exceptions\CategoryNotFoundException;
use App\Model\Category;

class CategoryRepository
{

    private $categoryEloquent;

    public function __construct()
    {
        $this->categoryEloquent = new Category();
    }

    /**
     * @param $name
     * @return mixed
     * @throws CategoryNotFoundException
     */
    public function getCategoryIdByName($name)
    {
        $name = strtolower($name);
        $categoryObj = $this->categoryEloquent->where('name', ucfirst($name))->first();
        if ($categoryObj == null)
            throw new CategoryNotFoundException();
        return $categoryObj->id;
    }
}