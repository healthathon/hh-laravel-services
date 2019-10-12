@extends('admin.layouts.template')
@section('stylesheet')
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
                        {{ $categoryName }} Weekly Task
                    </h4>
                    <div class="alert error">

                    </div>
                    <div class="basic-form">
                        <form enctype="multipart/form-data" id="updateWeekInfo" method="post">
                            {{ csrf_field() }}
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Select Week</label>
                                <div class="col-sm-9">
                                    <select id="week" name="week" class="form-control">
                                        @foreach($weeksCountArr as $week)
                                            <option value="{{ $week }}">{{ $week }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- Since days of weeks are always 7--}}
                            @for($i = 1 ; $i <= 7 ; $i++)
                                <div class='form-group row align-items-center'>
                                    <label class='col-sm-3 col-form-label text-label'>Day{{$i}} Title</label>
                                    <div class='col-sm-9'>
                                        <div class='input-group'>
                                            <input type='text' class='form-control' placeholder='Day{{$i}} Title'
                                                   id='day{{$i}}_title' name='day{{$i}}_title'
                                                   value='{{ $taskInfo["day".$i."_title"]  }}'
                                                   required='required'>
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group row align-items-center'>
                                    <label class='col-sm-3 col-form-label text-label'>Day{{$i}} Message</label>
                                    <div class='col-sm-9'>
                                        <div class='input-group'>
                                            <input type='text' class='form-control' placeholder='Day{{$i}} Message'
                                                   id='day{{$i}}_message' name='day{{$i}}_message'
                                                   value='{{ $taskInfo["day".$i."_message"]  }}'
                                                   required='required'>
                                        </div>
                                    </div>
                                </div>
                            @endfor

                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Upload Image</label>
                                <div class="col-sm-9">
                                    <input type="file" name="image" id="image" accept="image/*"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-primary btn-forms" id="updateForm">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $("#week").on('change', function (e) {
            let weekNo = $(this).val();
            getTaskInformation("{{ $taskBank_id }}", weekNo);
        });
        $("#updateWeekInfo").on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let urlToRequest = "/admin/task/taskBank/" + {{ $taskBank_id }} +"/week/" + formData.get('week');
            let titleObj = {
                'day1_title': formData.get('day1_title'),
                'day2_title': formData.get('day2_title'),
                'day3_title': formData.get('day3_title'),
                'day4_title': formData.get('day4_title'),
                'day5_title': formData.get('day5_title'),
                'day6_title': formData.get('day6_title'),
                'day7_title': formData.get('day7_title')
            };
            let messageObj = {
                'day1_message': formData.get('day1_message'),
                'day2_message': formData.get('day2_message'),
                'day3_message': formData.get('day3_message'),
                'day4_message': formData.get('day4_message'),
                'day5_message': formData.get('day5_message'),
                'day6_message': formData.get('day6_message'),
                'day7_message': formData.get('day7_message')
            };
            $.ajax({
                method: "PUT",
                url: window.location.origin + urlToRequest,
                data: {
                    _token: '{{ csrf_token() }}',
                    titleObj: titleObj,
                    messageObj: messageObj
                },
                success: function (data) {
                    if (data.statusCode === 500) {
                        alert("Something went wrong");
                    } else {
                        console.log(data.statusMessage);
                    }
                    $("html, body").animate({scrollTop: 0}, "slow");
                },
                error: function (data) {
                }
            });
        });
        function getTaskInformation(taskBankId, weekNo = 1) {
            let urlToRequest = "/admin/task/taskBank/" + taskBankId + "/week/" + weekNo;
            $.ajax({
                method: "GET",
                contentType: false,
                processData: false,
                dataType: 'json',
                url: window.location.origin + urlToRequest,
                data: {
                    weekNo: weekNo
                },
                success: function (data) {
                    if (data.statusCode === 500) {
                        alert("Something went wrong");
                    } else {
                        renderDynamicContent(data.response)
                    }
                    $("html, body").animate({scrollTop: 0}, "slow");
                },
                error: function (data) {
                }
            });
        }
        function renderDynamicContent(getTaskInfo) {
            for (let i = 1; i <= 7; i++) {
                $("#day" + i + "_title").val(getTaskInfo["day" + i + "_title"]);
                $("#day" + i + "_message").val(getTaskInfo["day" + i + "_message"]);
            }
        }
    </script>
@endsection
