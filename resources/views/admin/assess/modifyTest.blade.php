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
                    <h4 class="card-title mb-4">Test Mapping To Assessment Answers</h4>
                </div>
                <div class="card-body">
                    <div class="alert hide">This is a primary alert—check it out!</div>
                    <div class="basic-form">
                        <form>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Question</label>
                                <div class="col-sm-9">
                                    <label class="text-label">{{ $query["query"] }}</label>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Selected Answer</label>
                                <div class="col-sm-9">
                                    <select id="answer" name="answer" class="form-control">
                                        @foreach($answers as $answer)
                                            <option value="{{ $answer }}">{{ $answer }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Recommended Test</label>
                                <div class="col-sm-9">
                                    <br/>
                                    <div class="row labsTestBox" id="assessmentTests">
                                        @foreach($assessmentTests as $assessmentTest)
                                            <div class="col-sm-6 col-lg-4 col-xl-4">
                                                <div class="form-check mb-5 mr-5">
                                                    <input id="{{ $assessmentTest['id'] }}_checkbox"
                                                           class="form-check-input styled-checkbox"
                                                           value='{{ $assessmentTest['id'] }}'
                                                           {{ in_array($assessmentTest['id'],$recommendedIds) ? "checked='checked" : "" }}
                                                           type="checkbox"/>
                                                    <label for="{{ $assessmentTest['id'] }}_checkbox"
                                                           class="form-check-label check-purple">{{ $assessmentTest["test_name"] }}</label>
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
            console.log(checkedTestId);
            $.ajax({
                method: "POST",
                url: "{{route('admin.assess.test.recommend.modify')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    query_id: "{{$query["id"]}}",
                    answer: $("#answer").val(),
                    recommended_test: checkedTestId
                },
                success: function (data) {
                    if (data['status']) {
                        $(".alert").addClass("alert-success")
                            .removeClass("hide").text("Updated");
                    } else {
                        $(".alert").addClass("alert-danger")
                            .removeClass("hide")
                            .text(data['message']);
                    }
                    setTimeout(function () {
                        $(".alert").addClass("hide")
                    }, 1000);
                },
                error: function (data) {
                }
            });
        });
        $("#answer").on('change', function (e) {
            e.preventDefault();
            const value = $(this).val();
            const queryId = "{{$query["id"]}}";
            let url = "{{ route('admin.assess.query.answers.get',[':queryId',':answer']) }}";
            url = url.replace(":queryId", queryId).replace(":answer", value);
            $.ajax({
                method: "GET",
                url: url,
                success: function (data) {
                    $(".alert").addClass("hide");
                    if (data['status']) {
                        $("#assessmentTests").html("");
                        const testIds = data['recommended_test'];
                        const allTests = data['tests'];
                        $.each(allTests, function (k, v) {
                            if (testIds.includes(v['id'])) {
                                $("#assessmentTests").append(checked(v['id'], v['test_name']));
                            } else {
                                $("#assessmentTests").append(unchecked(v['id'], v['test_name']));
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