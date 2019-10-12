<?php

namespace App\Respositories;


use App\Exceptions\BlogNotFoundException;
use App\Model\Blog;

class BlogRepository
{

    function fetchBlogs()
    {
        return Blog::all();
    }

    public function blogInfoById(int $id)
    {
        $blogInfo = Blog::where('id', $id)->first();
        $blogInfo->published_date = date_format(date_create($blogInfo->published_date), "Y-m-d");
        return $blogInfo;
    }

    function saveBlog($data)
    {
        return Blog::create($data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     * @throws BlogNotFoundException
     */
    function updateBlog(array $data, int $id)
    {
        $blog = Blog::where('id', $id)->first();
        if ($blog == null)
            throw new BlogNotFoundException();
        return Blog::where('id', $id)->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws BlogNotFoundException
     */
    public function deleteBlog(int $id)
    {
        $blog = Blog::where('id', $id)->first();
        if ($blog == null)
            throw new BlogNotFoundException();
        return Blog::where('id', $id)->delete();
    }
}