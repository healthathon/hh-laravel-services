@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        .hide {
            display: none;
        }

        * {
            color: black;
        }

        .ellipsis {
            word-break: break-word;
            text-overflow: ellipsis;
        }
    </style>
@endsection

@section('main-content')
    <div class="row page-titles">
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Recommended Test on BMI Answers</h4>
                    <div class="alert hide">This is a primary alert—check it out!</div>
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
                controller: {
                    loadData: function (item) {
                        return $.ajax({
                            method: "GET",
                            dataType: "json",
                            url: "{{route('admin.bmi.test.recommend.get')}}",
                        }).done(function (result) {
                        })
                    }
                },
                fields: [
                    {name: "id", type: "text"},
                    {name: "deviation_range", type: "text", title: "Deviation Range"},
                    {
                        name: "recommended_test",
                        type: "text",
                        width: 100,
                        css: "text-center ellipsis",
                        title: "Recommended Tests",
                        validate: {
                            validator: "required",
                            message: function () {
                                return "Please Enter a Recommend Test Name";
                            }
                        }
                    },
                    {
                        name: "update_recommend_test",
                        type: "text",
                        width: 100,
                        css: "text-center",
                        title: "Update Recommend",
                        validate: {
                            validator: "required",
                            message: function () {
                                return "Please Enter a Test Name";
                            }
                        },
                        itemTemplate: function (item, data) {
                            let url = "{{ route("admin.bmi.test.recommend.modify.page",[":bmiId"]) }}";
                            url = url.replace(":bmiId", data.id);
                            return "<a href='" + url + "' target='_blank'>Modify Test Recommendation</a>";
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
    </script>
@endsection