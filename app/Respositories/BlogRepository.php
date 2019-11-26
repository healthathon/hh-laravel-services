<?php

namespace App\Respositories;


use App\Exceptions\BlogNotFoundException;
use App\Model\Blog;
use Illuminate\Database\Eloquent\Model;

class BlogRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new Blog());
    }

    function fetchBlogs()
    {
        return $this->model->all();
    }

    public function blogInfoById(int $id)
    {
        $blogInfo = $this->model->where('id', $id)->first();
        $blogInfo->published_date = date_format(date_create($blogInfo->published_date), "Y-m-d");
        return $blogInfo;
    }

    function saveBlog($data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     * @throws BlogNotFoundException
     */
    function updateBlog(array $data, int $id)
    {
        $blog = $this->model->where('id', $id)->first();
        if ($blog == null)
            throw new BlogNotFoundException();
        return $this->model->where('id', $id)->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws BlogNotFoundException
     */
    public function deleteBlog(int $id)
    {
        $blog = $this->model->where('id', $id)->first();
        if ($blog == null)
            throw new BlogNotFoundException();
        return $this->model->where('id', $id)->delete();
    }
}