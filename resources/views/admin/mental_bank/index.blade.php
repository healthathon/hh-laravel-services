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
                <li class="breadcrumb-item active">Mental Bank Questions</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">About Mental Bank Question</h4>
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
                height: "600px",

                editing: false,
                deleting: false,
                inserting: false,
                sorting: false,
                paging: true,
                filtering: false,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,

                // data:data,
                controller: {
                    loadData: function (item) {
                        return $.ajax({
                            method: "GET",
                            dataType: "json",
                            url: "{{route('admin.mntl_bank.all')}}",
                        }).done(function (result) {
                        })
                    }
                },
                fields: [
                    {name: "id", type: "number", css: 'hide'},
                    {name: "query", type: "text", title: "Query"},
                    {name: "answer", type: "text", title: "Answer"},
                    {
                        name: "recommended_regimen",
                        type: "text",
                        width: 100,
                        css: "text-center ellipsis",
                        title: "Recommended Regimen",
                        validate: {
                            validator: "required",
                            message: function () {
                                return "Please Enter a Test Name";
                            }
                        }
                    },
                    {
                        name: "change_test",
                        type: "text",
                        width: 100,
                        css: "text-center",
                        title: "Update Recommend",
                        validate: {
                            validator: "required",
                            message: function () {
                                return "Please Enter a Regimen Name";
                            }
                        },
                        itemTemplate: function (value, item) {
                            const $link = show(item);
                            return $("<div>").append($link);
                        }
                    }
                ],
                confirmDeleting: true,
                deleteConfirm: "Are you sure?",
                noDataContent: "Not Test Found"
            });
        });

        /**
         *  Generalize Method to add,update and delete test
         * @param method: Http Method
         * @param url: Url to Request
         * @param item: Payload
         * @param success_message: Message for Success
         * @returns {*}
         */
        function processRequest(method, url, item, success_message) {
            return $.ajax({
                method: method,
                url: url,
                data: {
                    item,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
            }).done(function (result) {
                $(".alert").addClass("alert-success")
                    .removeClass("hide")
                    .text(success_message);
                setTimeout(function () {
                    $(".alert").addClass("hide")
                }, 1000);
            });
        }

        // Redirect to modify page
        function show(item) {
            let url = "{{route("admin.assess.query.answers.page",[":queryId",":answer"])}}";
            url = url.replace(":queryId", item.query_id).replace(":answer", item.answer);
            return "<a href=\"" + url + "\" target=\"_blank\">Modify Recommendation</a>";
        }

        // For Reloading Data in case of any updates
        $(window).focus(function () {
            $("#jsGrid-basic").jsGrid("loadData");
        });
    </script>
@endsection
