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
                                        @foreach($tests as $test)
                                            <div class="col-sm-6 col-lg-4 col-xl-4">
                                                <div class="form-check mb-5 mr-5">
                                                    <input id="{{ $test['id'] }}_checkbox"
                                                           class="form-check-input styled-checkbox"
                                                           value='{{ $test['id'] }}'
                                                           {{ in_array($test['id'],$recommendedIds) ? "checked='checked" : "" }}
                                                           type="checkbox"/>
                                                    <label for="{{ $test['id'] }}_checkbox"
                                                           title="{{ $test["test_name"]  }}"
                                                           class="form-check-label check-purple">
                                                        {{$test["test_name"] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary btn-forms" id="modifyTestBtn">
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
        $("#modifyTestBtn").on('click', function (e) {
            e.preventDefault();
            let checkedTestId = [];
            let i = 0;
            $('input[type=checkbox]:checked').map(function (_, el) {
                checkedTestId[i] = parseInt($(el).val());
                i++;
            });
            $.ajax({
                method: "POST",
                url: "{{ route('admin.sha.test.recommend.info.update') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    query_id: "{{$query["id"]}}",
                    answer_id: $("#answer").val(),
                    recommended_test: checkedTestId
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
            let url = "{{route('admin.sha.test.recommend.info.select.fetch',[":queryId",":answer"])}}";
            url = url.replace(":queryId", queryId).replace(":answer", value);
            $.ajax({
                method: "GET",
                url: url,
                success: function (response) {
                    $(".alert").addClass("hide");
                    if ("data" in response) {
                        $("#regimenTests").html("");
                        const regimenIds = response["data"]['recommended_test'];
                        const regimens = response["data"]['tests'];
                        $.each(regimens, function (k, v) {
                            let title = v['test_name'];
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