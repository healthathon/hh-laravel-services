@extends('admin.layouts.template')
@section('stylesheet')
    <link href="{{ url('admin/css/bootstrap-tagsinput.css') }}" rel="stylesheet"/>
    <script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>
    <style type="text/css">
        .error {
            display: none;
        }
    </style>
@endsection

@section('main-content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card forms-card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        {{ $isEditPage ? "Edit" : "Add" }} Blog
                    </h4>
                    <div class="alert error">

                    </div>
                    <div class="basic-form">
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Title</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Blog Title" id="title"
                                           value="{{ isset($blogInfo) ? $blogInfo->title : "" }}"
                                           required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Source Link</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Source Link"
                                           id="source_link"
                                           value="{{ isset($blogInfo) ? $blogInfo->source_link : "" }}"
                                           required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Keywords</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input class="tags" id="tags_1" placeholder="Add Keywords"
                                           type="text" style="width: 100%"
                                           value="{{ isset($blogInfo) ? $blogInfo->keywords : "" }}"
                                           required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Categories</label>
                            <div class="col-sm-9">
                                <select id="categories" id="categories" class="form-control">
                                    <option value="3" {{  isset($blogInfo) && $blogInfo->categories == 3 ? "selected='selected'" : ""}}>
                                        Nutrition
                                    </option>
                                    <option value="1" {{ isset($blogInfo) && $blogInfo->categories == 1 ? "selected='selected'" : ""}}>
                                        Physics
                                    </option>
                                    <option value="4" {{ isset($blogInfo) && $blogInfo->categories == 4 ? "selected='selected'" : ""}}>
                                        Lifestyle
                                    </option>
                                    <option value="2" {{ isset($blogInfo) && $blogInfo->categories == 2 ? "selected='selected'" : ""}}>
                                        Mental
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Description</label>
                            <div class="col-sm-9">
                                    <textarea id="description">
                                        {{ isset($blogInfo) ? $blogInfo->description : "" }}
                                    </textarea>
                                <script>
                                    CKEDITOR.replace('description');
                                </script>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Summary Title</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Summary Title"
                                           id="summary_title"
                                           value="{{ isset($blogInfo) ? $blogInfo->summary_title : "" }}"
                                           required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Original Article Link</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Original Article Link"
                                           id="original_article_link"
                                           value="{{ isset($blogInfo) ? $blogInfo->original_article_link : "" }}"
                                           required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Image Link</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Image Link"
                                           id="free_image_link"
                                           value="{{ isset($blogInfo) ? $blogInfo->free_image_link : "" }}"
                                           required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-label">Published Date</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="date" class="form-control" placeholder="Published Date"
                                           id="published_date"
                                           value="{{ isset($blogInfo) ? $blogInfo->published_date : "" }}"
                                           required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-primary btn-forms" id="addBlog">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{asset('admin/js/bootstrap-tagsinput.min.js')}}"></script>
    <script src="{{asset('admin/js/plugins-init/bootstrap-tagsinput-init.js')}}"></script>
    <script type="text/javascript">
        $("#addBlog").on('click', function (e) {
            e.preventDefault();
            let errorRef = $(".error");
            let description = CKEDITOR.instances.description.getData();
            if ($("#title").val().trim() === "" || !description) {
                errorRef.css("display", "block");
                errorRef.addClass("alert-danger");
                errorRef.html("<p> Title & Description are required </p>");
                return false;
            }
            $.ajax({
                method: "{{ $isEditPage ? 'PUT' : 'POST' }}",
                url: "{{ $isEditPage ? route('admin.blog.update',['id' => $blogInfo->id]) : route('admin.blog.save')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    title: $("#title").val(),
                    source_link: $("#source_link").val(),
                    keywords: $("#tags_1").val(),
                    categories: $("#categories").val(),
                    summary_title: $("#summary_title").val(),
                    original_article_link: $("#original_article_link").val(),
                    free_image_link: $("#free_image_link").val(),
                    published_date: $("#published_date").val(),
                    description: description
                },
                success: function (data) {
                    errorRef.css("display", "block");
                    if ("error" in data) {
                        errorRef.addClass("alert-danger");
                        errorRef.text(data["error"]);
                    } else {
                        errorRef.addClass("alert-success");
                        errorRef.text(data["data"]);
                        errorRef.delay(2000).slideUp();
                    }
                    $("html, body").animate({scrollTop: 0}, "slow");
                },
                error: function (data) {
                }
            });
        });
    </script>
@endsection
