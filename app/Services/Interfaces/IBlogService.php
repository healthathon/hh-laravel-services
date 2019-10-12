<?php

namespace App\Services\Interfaces;


interface IBlogService
{

    function all();

    function postBlog(array $data);

    function updateBlog(array $data, int $id);

    function getBlogByCategory(string $category);

    function deleteBlog(int $id);
}