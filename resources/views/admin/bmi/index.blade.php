@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        .hide {
            display: none;
        }
    </style>
@endsection

@section('main-content')
    <div class="row page-titles">
        <div class="col p-md-0">
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">BMI-Score</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">About BMI Score</h4>
                    <div class="alert alert-success hide" id="updateMessage">
                        Message
                    </div>
                    <div id="jsGrid-basic"></div>
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
            'use strict';
            $("#jsGrid-basic").jsGrid({
                width: "100%",
                height: "400px",
                editing: true,
                deleting: false,
                inserting: false,
                sorting: false,
                paging: false,
                filtering: false,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,
                search: false,
                controller: {
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            url: "{{route('admin.bmi-get-score')}}",
                        }).done(function (result) {
                            console.log(result);
                        })
                    },
                    updateItem: function (item) {
                        return $.ajax({
                            method: "patch",
                            url: "{{route('admin.bmi-update-score')}}",
                            data: {
                                id: item['id'],
                                score: item["score"],
                                deviation_range: item["deviation_range"],
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showMessage(result);
                            $("#jsGrid-basic").jsGrid("loadData");
                        });
                    }
                },
                fields: [
                    {
                        name: "id",
                        type: "read-only",
                        width: 70,
                        validate: "required",
                        css: "text-center",
                        title: "Serial Id"
                    },
                    {
                        name: "deviation_range",
                        type: "read-only",
                        width: 70,
                        validate: "required",
                        css: "text-center",
                        title: "Deviation Range"
                    },
                    {
                        name: "score",
                        type: "text",
                        width: 70,
                        validate: "required",
                        css: "text-center",
                        title: "BMI Score"
                    },
                    {type: "control", deleteButton: false}
                ]
            });
        });

        function showMessage(response) {
            const updateMessageRef = $("#updateMessage");
            updateMessageRef.removeClass("hide");
            if ("error" in response) {
                updateMessageRef.addClass("alert-danger").text(response["error"]);
            } else {
                updateMessageRef.addClass("alert-success").text(response["data"]);
            }
            updateMessageRef.delay(2000).slideUp();
        }
    </script>
@endsection