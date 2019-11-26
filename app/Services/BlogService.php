<?php

/**
 * This Service Class provides services related to blogs module
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Services
 * @version  v.1.1
 */

namespace App\Services;


use App\Model\Blog;
use App\Model\Category;
use App\Respositories\BlogRepository;
use App\Respositories\CategoryRepository;
use App\Services\Interfaces\IBlogService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class BlogService
 *
 * This service provides users to read blogs and admin to update and add blogs.
 * @package App\Services
 */
class BlogService implements IBlogService
{

    private $blogRepoObj;

    public function __construct()
    {
        $this->blogRepoObj = new BlogRepository();
    }

    public function all()
    {
        return $this->blogRepoObj->fetchBlogs();
    }

    /**
     * Add Blog Functionality
     *
     * @param array $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postBlog(array $request)
    {
        $request["description"] = trim($request["description"]);
        return $this->blogRepoObj->saveBlog($request);
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     * @throws \App\Exceptions\BlogNotFoundException
     */
    public function updateBlog(array $data, int $id)
    {
        unset($data["_token"]);
        return $this->blogRepoObj->updateBlog($data, $id);
    }

    /**
     *  Reference : http://php.net/manual/en/features.file-upload.multiple.php
     * @param $file_post
     * @return array
     */
    public function reArrayFiles(&$file_post)
    {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

    /**
     *  Fetching all data from blog table and for the first time it will
     *  store to file cache and then from next request  it will fetch data
     * from cache unless it get new updates to blogs
     * @param $categoryName
     * @return array
     */
    public function getBlogByCategory(string $categoryName)
    {
        $categoryId = (new CategoryRepository())->where('name', $categoryName)->first()->id;
        $blogs = Blog::where('categories', $categoryId)->get();
        return $blogs;
    }

    public function getBlogInfoById(int $id)
    {
        return $this->blogRepoObj->blogInfoById($id);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \App\Exceptions\BlogNotFoundException
     */
    public function deleteBlog(int $id)
    {
        return $this->blogRepoObj->deleteBlog($id);
    }
}