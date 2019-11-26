<?php

namespace App\Http\Controllers\Api;

use App\Helpers;
use App\Model\Category;
use App\Respositories\CategoryRepository;
use App\Services\BlogService;
use App\Http\Controllers\Controller;

/**
 * Class BlogController
 * @package App\Http\Controllers\Api
 */
class BlogController extends Controller
{
    private $blogService;
    private $categoryRepo;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->blogService = new BlogService();
        $this->categoryRepo = $categoryRepository;
    }

    /**
     * BlogService@getAllBlog
     * @param $categoryName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBlog(string $categoryName)
    {
        $blogs = $this->blogService->getBlogByCategory($categoryName);
        return Helpers::getResponse(200, ucfirst($categoryName) . " Blogs", $blogs);
    }

    /**
     * This function returns blog categories name
     */
    public function getBlogCategories()
    {
        $categoryInfo = [];
        $categories = $this->categoryRepo->all();
        foreach ($categories as $category) {
            $categoryInfo[] = [
                'name' => $category->mapCategoryName($category->name),
                'value' => $category->name == "Physics" ? "physics" : strtolower($category->name)
            ];
        }
        // Adding Extra Category Others as this category is for blogs only
        $categoryInfo[] = [
            'name' => 'Others',
            'value' => 'others'
        ];
        return Helpers::getResponse(200, "Blogs Categories", $categoryInfo);
    }
}
