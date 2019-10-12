<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BlogNotFoundException;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    private $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Show all blogs present in database
     * @author  Mayank Jariwala
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBlogs()
    {
        $data = $this->blogService->all();
        return view('admin.blog.show', ['response' => $data]);
    }

    /**
     *  Return  Edit Blog Page with existing  information
     * @author  Mayank Jariwala
     * @param $action
     * @param int $id : Existing Blog Id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderAddOrEditBlogPage($action, $id = 0)
    {
        $isEditPage = $action === "add" ? false : true;
        $blogInfo = null;
        if ($id != 0)
            $blogInfo = $this->getBlogInfoById($id);
        return view('admin.blog.addOrEdit', ['isEditPage' => $isEditPage, 'blogInfo' => $blogInfo]);
    }

    private function getBlogInfoById(int $id)
    {
        return $this->blogService->getBlogInfoById($id);
    }

    /**
     * @author  Mayank Jariwala
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postBlog(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'source_link' => 'required',
            'free_image_link' => 'required',
            'keywords' => 'required',
            'description' => 'required',
            'summary_title' => 'required',
            'original_article_link' => 'required',
            'published_date' => 'required'
        ]);
        if ($validate->fails())
            return Helpers::getResponse(400, $validate->getMessageBag());
        $request->merge(['published_date' => date_format(date_create($request->get('published_date')), "M d, Y")]);
        try {
            $this->blogService->postBlog($request->all());
            return Helpers::sendResponse(["data" => "Blog Saved Successfully"]);
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }


    /**
     * Return response of saving an edited blog information
     *
     * @author  Mayank Jariwala
     * @param Request $request
     * @param $id : Blog id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBlog(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'source_link' => 'required',
            'free_image_link' => 'required',
            'keywords' => 'required',
            'description' => 'required',
            'summary_title' => 'required',
            'original_article_link' => 'required',
            'published_date' => 'required'
        ]);
        if ($validate->fails())
            return Helpers::sendResponse(["error" => $validate->getMessageBag()->first()]);
        $request->merge(['published_date' => date_format(date_create($request->get('published_date')), "M d, Y")]);
        try {
            $this->blogService->updateBlog($request->all(), $id);
            return Helpers::sendResponse(["data" => "Blog Updated"]);
        } catch (BlogNotFoundException $e) {
            return $e->sendBlogNotFoundException();
        }
    }

    /**
     *  Delete Blog
     * @param $id : Blog Id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBlogById($id)
    {
        try {
            $this->blogService->deleteBlog($id);
            return Helpers::getResponse(200, "Blog deleted");
        } catch (BlogNotFoundException $e) {
            return $e->sendBlogNotFoundException();
        }
    }
}
