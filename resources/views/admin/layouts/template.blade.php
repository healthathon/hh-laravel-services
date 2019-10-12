@include('admin.layouts.head')
<body>
<!--*******************
    Preloader start
********************-->
<div id="preloader">
    <div class="loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"/>
        </svg>
    </div>
</div>
<!--*******************
    Preloader end
********************-->

<div id="main-wrapper">
    @include('admin.layouts.header')
    @include('admin.layouts.sidebar')
    <div class="content-body">
        <div class="container-fluid">
            @if(Request::is('admin/home'))
                <div class="row page-titles">
                    <div class="col p-md-0">
                        <h4>Home</h4>
                    </div>
                    <div class="col p-md-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a>
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="row" id="dragdrop">
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-two">
                                    <div class="media">
                                        <div class="media-body">
                                            <span class="text-success">Registered Users</span>
                                            <h2 class="mt-0 mb-1 text-danger">{{ $noOfRegisteredUsers }}</h2>
                                        </div>
                                        <img class="ml-3" src="http://chittagongit.com/download/330842" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-two">
                                    <div class="media">
                                        <div class="media-body">
                                            <span class="text-success">Registered Labs</span>
                                            <h2 class="mt-0 mb-1 text-danger">{{ $noOfRegisteredLabs }}</h2>
                                        </div>
                                        <img class="ml-3"
                                             src="https://img.pngio.com/science-free-vector-lab-transparent-vector-clipart-psd-lab-png-free-512_512.png"
                                             alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4>Flush Old Task and Upload New Task</h4><br/>
                        <div class="alert alert-danger">
                            This functionality is currently under testing. Please Don't upload any files.
                        </div>
                        <div class="alert alert-primary uploadMessage" id="uploadMessage">
                            Uploaded data
                        </div>
                        <div class="form-group form-inline">
                            <input type="file" multiple="multiple" name="file[]" class="form-control" id="files"
                                   accept=".csv"/>
                            <button type="button" class="form-control btn btn-success" value="Upload"
                                    onclick="javascript:alert('Under Construction');"
                                    {{--id="flushAndUploadNewTask"--}}>
                                Upload
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4>Message For Task Done</h4><br/>
                        <div class="alert hide messageBox">
                        </div>
                        <div class="form-group">
                            <input type="text" name="taskCompleteMessage" class="form-control" id="taskCompleteMessage"
                                   placeholder="Enter message for daily task complete"
                                   style="width: 100%"/>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-success" id="saveTaskDoneMessage">Save Message
                            </button>
                        </div>
                    </div>
                </div>

            @endif
            @yield('main-content')
        </div>
    </div>
</div>


@include('admin.layouts.footer')

<!--**********************************
    Scripts
    ***********************************-->
<script src="{{asset('admin/assets/plugins/common/common.min.js')}}"></script>
<script src="{{asset('admin/js/custom.min.js')}}"></script>
<script src="{{asset('admin/js/settings.js')}}"></script>
<script src="{{asset('admin/js/gleek.js')}}"></script>
<script src="{{asset('admin/js/styleSwitcher.js')}}"></script>
@yield('script')
@if(Request::is('admin/home'))
    <script type="text/javascript">
        let files = document.getElementById("files");
        let filesList = [];
        const fileName = ["weekly_task.csv", "regimen.csv"];
        files.addEventListener('change', function () {
            if (files.files.length !== 2) {
                alert("Please upload 2 files");
                disableUploadButton();
                return false;
            }
            enableUploadButton();
            for (let i = 0; i < files.files.length; i++) {
                if (fileName.includes(files.files[i].name)) {
                    filesList.push(files.files[i]);
                } else {
                    invalidFileName();
                    break;
                }
            }
        });
        $("#flushAndUploadNewTask").on('click', function (e) {
                e.preventDefault();
                let formData = new FormData();
                formData.append("_token", "{{ csrf_token() }}");
                if (filesList[0].name === "regimen.csv") {
                    formData.append("regimen", filesList[0]);
                }
                if (filesList[1].name === "weekly_task.csv") {
                    formData.append("weekly_task", filesList[1]);
                }
                let classState = "alert";
                $.ajax({
                    method: "post",
                    url: "{{ url("admin/flushAndUpload/task") }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $("#uploadMessage").removeClass("uploadMessage").addClass(classState).text("Uploading & Processing File ...");
                    },
                    success: function (data) {
                        let response = JSON.parse(JSON.stringify(data));
                        classState = response.statusCode === 200 ? "alert alert-success" : "alert alert-danger";
                        $("#uploadMessage").addClass(classState).text(response.statusMessage);
                        setTimeout(function () {
                            $("#uploadMessage").addClass("uploadMessage");
                        }, 2000);
                    },
                    error: function (data) {
                    }
                });
            }
        );

        $("#saveTaskDoneMessage").on("click", function (e) {
            e.preventDefault();
            let value = $("#taskCompleteMessage").val();
            if (value.trim() === "") {
                alert("Please Enter Message");
                return false;
            } else {
                let messageBoxRef = $(".messageBox");
                messageBoxRef.removeClass("hide");
                $.ajax({
                    method: "post",
                    url: "{{ route('admin.task.complete.message') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        message: value.trim()
                    },
                    success: function (response) {
                        if ("error" in response)
                            messageBoxRef.addClass("alert alert-danger").text(response["error"]);
                        else
                            messageBoxRef.addClass("alert alert-success").text(response["data"]);
                    },
                    error: function (response) {
                        console.error(response);
                    },
                    beforeSend: function () {
                        messageBoxRef.addClass("alert alert-primary").text("Sending data...");
                    }
                });
            }
        });

        function invalidFileName() {
            alert("File names should be regimen.csv and weekly_task.csv");
            return false;
        }

        function disableUploadButton() {
            $("#flushAndUploadNewTask").attr("disabled", true);
        }

        function enableUploadButton() {
            $("#flushAndUploadNewTask").attr("disabled", false);
        }
    </script>
@endif

</body>



