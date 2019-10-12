@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/css/jquery.dataTables.min.css')}}">
    <style type="text/css">
        .error {
            display: none;
        }
    </style>
@endsection

@section('main-content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Blogs</h4>
                    <div class="alert error">
                    </div>
                    <div class="table-responsive">
                        <table class="table header-border" id="example">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Summary Title</th>
                                <th>Keywords</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($response as $info)
                                <tr>
                                    <td>{{ $info['id'] }}</td>
                                    <td>{{ $info['title'] }}</td>
                                    <td>{{ $info['summary_title'] }}</td>
                                    <td> {{ $info['keywords'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.blog.add_edit',['action' => 'edit','id' => $info['id']]) }}"><i
                                                    class="mdi mdi-pencil  text-success"
                                                    style="font-size: 18px;"></i></a>
                                        <a href="javascript:deleteBlog('{{$info['id']}}')"><i
                                                    class="mdi mdi-delete text-danger"
                                                    style="font-size: 18px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('admin/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/js/plugins-init/datatables.init.js')}}"></script>
    <script type="text/javascript">
        function deleteBlog(id) {
            let errorRef = $(".error");
            let response = confirm("Are you sure you want to delete?");
            if (response) {
                let url = "{{ route('admin.blog.delete',':id') }}";
                url = url.replace(":id", id);
                $.ajax({
                    method: "delete",
                    contentType: false,
                    processData: false,
                    url: url,
                    success: function (data) {
                        if (data.statusCode === 500) {
                            errorRef.css("display", "block");
                            errorRef.addClass("alert-danger");
                            if (Array.isArray(data.statusMessage)) {
                                $.each(data.statusMessage, function (key, values) {
                                    console.log("Rendering : " + key);
                                    errorRef.html("<p>" + values + "</p>");
                                });
                            } else {
                                errorRef.html("<p>" + data.statusMessage + "</p>");
                            }
                        } else {
                            errorRef.css("display", "block");
                            errorRef.removeClass("alert-danger").addClass("alert-success");
                            errorRef.html("<p class='text-success'>" + data.statusMessage + " Redirecting in 2 seconds</p>");
                            setInterval(function () {
                                window.location.href = "{{ route('admin.blog.fetch') }}";
                            }, 2000);
                        }
                        $("html, body").animate({scrollTop: 0}, "slow");
                    },
                    error: function (data) {
                        errorRef.html("<p class='text-success'>" + data + "</p>");
                    }
                });
            } else {
                // do nothing
            }
        }
    </script>
@endsection