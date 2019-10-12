@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        .fetch-data-loader {
            position: absolute;
            display: block;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            z-index: 10000;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .hide {
            display: none;
        }

        h4.text-position {
            margin: 23% auto;
        }
    </style>
@endsection

@section('main-content')
    <div class="hide fetch-data-loader">
        <h4 class="text-position text-white text-center" id="text-position"> Fetching Data..... </h4>
    </div>
    <div class="row page-titles">
        <div class="col p-md-0">
            <h4>Diagnostic Lab Tests</h4>
        </div>
        <div class="alert update-message hide">

        </div>

        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Diagnostic Lab</a>
                </li>
                <!-- <li class="breadcrumb-item"><a href="javascript:void(0)">Elements</a>
                </li> -->
                <li class="breadcrumb-item active">Thyrocare Tests</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="form-group col-xs-12 col-md-4">
                        <label class="text-label text-danger" for="searchId">Enter Test ID [1-{{ $count }}] </label>
                        <input type="text" value="" id="searchId" placeholder="Enter Test ID"
                               class="form-control"/>
                    </div>
                    <div class="form-group col-xs-12 col-md-4">
                        <button type="button" class="text-center btn btn-primary"
                                id="getInfo">
                            Get Info
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form enctype="multipart/form-data" id="updateWeekInfo" method="put">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" id="id" value="{{ $thyrocareTest["id"] }}"/>
                            {{-- Profile Name and Test Code--}}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Profile Name</label>
                                        <input type="text" name="profile" class="form-control"
                                               id="profile"
                                               placeholder="Profile Name" value="{{ $thyrocareTest["profile"] }}"
                                               required/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Test Name</label>
                                        <input type="text" name="test_name" class="form-control"
                                               id="test_name"
                                               placeholder="Sample Type" value="{{ $thyrocareTest["test_name"] }}"
                                               required/>
                                    </div>
                                </div>
                            </div>
                            {{-- Abbreviation and Sample Type--}}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Abbreviation</label>
                                        <input type="text" name="abbr" class="form-control"
                                               id="abbr"
                                               placeholder="Abbreviation" value="{{ $thyrocareTest["abbr"] }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Test Code</label>
                                        <input type="text" name="test_code" class="form-control"
                                               id="test_code"
                                               placeholder="Test Code" value="{{ $thyrocareTest["test_code"] }}"
                                               required/>
                                    </div>
                                </div>
                            </div>
                            {{-- Process Duration and Results Duration--}}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Process Duration</label>
                                        <input type="text" name="process_duration" class="form-control"
                                               placeholder="Process Duration"
                                               id="process_duration"
                                               value="{{ $thyrocareTest["process_duration"] }}"
                                               required/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Result Duration</label>
                                        <input type="text" name="result_duration" class="form-control"
                                               placeholder="Result Duration"
                                               id="result_duration"
                                               value="{{ $thyrocareTest["result_duration"] }}"
                                               required/>
                                    </div>
                                </div>
                            </div>
                            {{-- Parameters Tested and Parameters Tested Unit--}}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Parameters Tested</label>
                                        <input type="text" name="parameters_tested" class="form-control"
                                               placeholder="Parameters Tested"
                                               id="parameters_tested"
                                               value="{{ $thyrocareTest["parameters_tested"] }}"
                                               required/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Parameters Tested Unit</label>
                                        <input type="text" name="parameters_tested_unit" class="form-control"
                                               placeholder="Parameters Tested Unit"
                                               id="parameters_tested_unit"
                                               value="{{ $thyrocareTest["parameters_tested_unit"] }}"
                                               required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Price</label>
                                        <input type="text" name="price" class="form-control"
                                               placeholder="Price"
                                               id="price"
                                               value="{{ $thyrocareTest["price"] }}"
                                               required/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-label">Sample Type</label>
                                        <input type="text" name="sample_type" class="form-control"
                                               id="sample_type"
                                               placeholder="Sample Type" value="{{ $thyrocareTest["sample_type"] }}"
                                               required/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="about-test">About</label>
                                <textarea class="form-control" id="about-test" name="about"
                                          rows="5">{{ $thyrocareTest["about"] }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="age_group">Age Group</label>
                                <textarea class="form-control" id="age_group" name="age_group"
                                          rows="5">{!! $thyrocareTest["age_group"] !!}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="good_range">Good Range</label>
                                <textarea class="form-control" id="good_range" name="good_range"
                                          rows="5">{!! $thyrocareTest["good_range"] !!}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="reason_to_do">Reasons</label>
                                <textarea class="form-control" id="reason_to_do" name="reason_to_do"
                                          rows="5">{!! $thyrocareTest["reason_to_do"] !!}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="preparation">Preparations</label>
                                <textarea class="form-control" id="preparation" name="preparation"
                                          rows="5">{{ $thyrocareTest["preparation"] }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="results">Results</label>
                                <textarea class="form-control" id="results" name="results"
                                          rows="5">{{ $thyrocareTest["results"] }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="test_suggestions">Test Suggestions</label>
                                <textarea class="form-control" id="test_suggestions" name="test_suggestions"
                                          rows="5">{{ $thyrocareTest["test_suggestions"] }}</textarea>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="text-center btn btn-warning btn-forms enableEdit">
                                        Enable Edit
                                    </button>
                                    <button type="submit" class="text-center btn btn-primary btn-forms"
                                            disabled="disabled"
                                            id="updateForm">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-footer">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('admin/assets/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('admin/assets/plugins/jsgrid/js/jsgrid.min.js')}}"></script>
    <script>
        $(function ($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        $("li.page-item").on('click', function (e) {
            e.preventDefault();
        });
        $(document).ready(function () {
            $("input,textarea").attr("readonly", true);
            $("input#searchId").attr("readonly", false);
            $(".enableEdit").on('click', function (e) {
                e.preventDefault();
                $("input,textarea").attr("readonly", false);
                $("#updateForm").attr("disabled", false);
            });
        });
        $("#updateForm").on('click', function (e) {
            const testIdRef = $("#id");
            let url = window.origin + "/admin/labs/test/" + testIdRef.val() + "/update";
            e.preventDefault();
            const newObject = {
                id: testIdRef.val(),
                profile: $("#profile").val(),
                test_name: $("#test_name").val(),
                test_code: $("#test_code").val(),
                abbr: $("#abbr").val(),
                sample_type: $("#sample_type").val(),
                result_duration: $("#result_duration").val(),
                process_duration: $("#process_duration").val(),
                parameters_tested: $("#parameters_tested").val(),
                parameters_tested_unit: $("#parameters_tested_unit").val(),
                about: $("#about-test").val(),
                price: $("#price").val(),
                age_group: $("#age_group").val(),
                good_range: $("#good_range").val(),
                reason_to_do: $("#reason_to_do").val(),
                preparation: $("#preparation").val(),
                results: $("#results").val(),
                test_suggestions: $("#test_suggestions").val()
            };
            $.ajax({
                method: "put",
                dataType: 'json',
                url: url,
                data: {
                    __token: "{{csrf_token()}}",
                    updateData: newObject
                },
                success: function (data) {
                    if (data['status']) {
                        renderData(data['data']);
                        $(".update-message").removeClass('hide').addClass("alert-success").html(data['message']);
                        $("html, body").animate({scrollTop: 0}, "slow");
                    } else {
                        $(".update-message").removeClass('hide').addClass("alert-danger").html(data['message']);
                        alert("Something Went Wrong" + data['message']);
                    }
                    setTimeout(function () {
                        $(".update-message").addClass("hide");
                        $("input,textarea").attr("readonly", true);
                        $("input#searchId").attr("readonly", false);
                        $("#updateForm").attr("disabled", true);
                    }, 1000);
                }
            });
        });
        $("#getInfo").on('click', function (e) {
            e.preventDefault();
            let value = $("#searchId").val();
            if (value === "" || isNaN(value)) {
                alert("Please Enter Some Value and it should be number");
            } else if (value > {{ $count }}) {
                alert("No Test found for such id");
            } else {
                getThyrocareTestDataFromId(parseInt(value));
            }
        });

        function getThyrocareTestDataFromId(id) {
            let url = window.origin + "/admin/labs/test/" + id + "/info";
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'json',
                processData: false,
                beforeSend: function (e) {
                    $(".fetch-data-loader").removeClass("hide");
                    $("h4#text-position").text("Fetching Information for Test #" + id);
                },
                success: function (data) {
                    $(".fetch-data-loader").addClass("hide");
                    renderData(data);
                },
                error: function () {
                }
            })
        }

        function renderData(data) {
            $("#id").val(data.id);
            $("#profile").val(data.profile);
            $("#test_name").val(data.test_name);
            $("#test_code").val(data.test_code);
            $("#abbr").val(data.abbr);
            $("#sample_type").val(data.sample_type);
            $("#result_duration").val(data.result_duration);
            $("#process_duration").val(data.process_duration);
            $("#parameters_tested").val(data.parameters_tested);
            $("#parameters_tested_unit").val(data.parameters_tested_unit);
            $("#about-test").val(data.about);
            $("#price").val(data.price);
            $("#age_group").val(data.age_group);
            $("#good_range").val(data.good_range);
            $("#reason_to_do").val(data.reason_to_do);
            $("#preparation").val(data.preparation);
            $("#results").val(data.results);
            $("#test_suggestions").val(data.test_suggestions);
        }
    </script>
@endsection