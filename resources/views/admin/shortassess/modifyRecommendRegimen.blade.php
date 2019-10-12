@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        * {
            color: black;
        }

        .hide {
            display: none;
        }

        input[type=checkbox].styled-checkbox + label {
            word-break: break-word;
        }

        .labsTestBox {
            max-height: 400px;
            overflow-y: scroll;
        }
    </style>
@endsection

@section('main-content')
    <div class="row page-titles">
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-4">Regimen for {{ $query["header"] }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert hide">This is a primary alert—check it out!</div>
                    <div class="basic-form">
                        <form>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Question</label>
                                <div class="col-sm-9">
                                    <label class="text-label">{{ $query["question"] }}</label>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Selected Answer</label>
                                <div class="col-sm-9">
                                    <select id="answer" name="answer" class="form-control">
                                        @foreach($query["answers"] as $answer)
                                            <option value="{{ $answer->id }}">{{ $answer->answer }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Regimens</label>
                                <div class="col-sm-9">
                                    <br/>
                                    <div class="row labsTestBox" id="regimenTests">
                                        @foreach($regimens as $regimen)
                                            <div class="col-sm-6 col-lg-4 col-xl-4">
                                                <div class="form-check mb-5 mr-5">
                                                    <input id="{{ $regimen['id'] }}_checkbox"
                                                           class="form-check-input styled-checkbox"
                                                           value='{{ $regimen['id'] }}'
                                                           {{ in_array($regimen['id'],$recommendedIds) ? "checked='checked" : "" }}
                                                           type="checkbox"/>
                                                    <label for="{{ $regimen['id'] }}_checkbox"
                                                           title="{{ $regimen["task_name"]  }}"
                                                           class="form-check-label check-purple">
                                                        {{ empty($regimen["title"]) ? $regimen["task_name"] : $regimen["title"] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary btn-forms" id="modifyRecommendBtn">
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
        $("#modifyRecommendBtn").on('click', function (e) {
            e.preventDefault();
            let checkedTestId = [];
            let i = 0;
            $('input[type=checkbox]:checked').map(function (_, el) {
                checkedTestId[i] = parseInt($(el).val());
                i++;
            });
            $.ajax({
                method: "POST",
                url: "{{ route('admin.sha.task.recommend.info.update') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    query_id: "{{$query["id"]}}",
                    answer_id: $("#answer").val(),
                    recommended_regimen: checkedTestId
                },
                success: function (response) {
                    if ("error" in response) {
                        $(".alert").addClass("alert-danger")
                            .removeClass("hide").text(response["error"]);
                    } else {
                        $(".alert").addClass("alert-success")
                            .removeClass("hide")
                            .text(response['data']);
                    }
                },
                error: function (data) {
                }
            });
        });
        $("#answer").on('change', function (e) {
            e.preventDefault();
            const value = $(this).val();
            const queryId = "{{$query["id"]}}";
            let url = "{{route('admin.sha.task.recommend.info.select.fetch',[":queryId",":answer"])}}";
            url = url.replace(":queryId", queryId).replace(":answer", value);
            $.ajax({
                method: "GET",
                url: url,
                success: function (response) {
                    $(".alert").addClass("hide");
                    if ("data" in response) {
                        $("#regimenTests").html("");
                        const regimenIds = response["data"]['recommended_task'];
                        const regimens = response["data"]['regimens'];
                        $.each(regimens, function (k, v) {
                            let title = v['title'] === "" ? v['task_name'] : v['title'];
                            if (regimenIds.includes(v['id'])) {
                                $("#regimenTests").append(checked(v['id'], title));
                            } else {
                                $("#regimenTests").append(unchecked(v['id'], title));
                            }
                        });
                    } else {
                    }
                },
                beforeSend: function () {
                    $(".alert").addClass("alert-warning")
                        .removeClass("hide").text("Fetching data.....");
                },
                error: function (data) {
                }
            });
        });

        // Show Item with checkbox
        function checked(id, test) {
            return "<div class=\"col-sm-6 col-lg-4 col-xl-4\">\n" +
                "                                <div class=\"form-check mb-5 mr-5\">\n" +
                "                                <input id=\"" + id + "_checkbox\"\n" +
                "                        class='form-check-input styled-checkbox'" +
                "                            value=\'" + id + "\'" +
                " checked=\"checked\"" +
                "                                 type=\"checkbox\" " + "/>" +
                "                                <label for=\"" + id + "_checkbox\"\n" +
                "                                class=\"form-check-label check-purple\">" + test + "</label>\n" +
                "                                </div>\n" +
                "                                </div>"
        }

        // show item with uncheck
        function unchecked(id, test) {
            return "<div class=\"col-sm-6 col-lg-4 col-xl-4\">\n" +
                "                                <div class=\"form-check mb-5 mr-5\">\n" +
                "                                <input id=\"" + id + "_checkbox\"\n" +
                "                        class='form-check-input styled-checkbox'" +
                "                            value=\'" + id + "\'" +
                "                                 type=\"checkbox\" " + "/>" +
                "                                <label for=\"" + id + "_checkbox\"\n" +
                "                                class=\"form-check-label check-purple\">" + test + "</label>\n" +
                "                                </div>\n" +
                "                                </div>"
        }
    </script>
@endsection