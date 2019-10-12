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
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Test Mapping To Assessment Answers</h4>
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

                // data:data,
                controller: {
                    loadData: function (item) {
                        return $.ajax({
                            method: "GET",
                            dataType: "json",
                            url: "{{route('admin.assess.test.recommend.get')}}",
                        }).done(function (result) {
                        })
                    },
                    insertItem: function (item) {
                        {{--return processRequest("post", "{{url('admin/assess/0/add/test')}}", item, "Test Added Successfully");--}}
                    },
                    deleteItem: function (item) {
                        const url = window.location.origin + "/admin/assess/test/" + item.id + "/delete";
                        // return processRequest("delete", url, item, "Deleted Successfully");
                    },
                    updateItem: function (item) {
                        {{--return processRequest("put", "{{url('admin/assess/1/update/test')}}", item, "Test Updated Successfully");--}}
                    }
                },
                fields: [
                    {name: "id", type: "number", css: 'hide'},
                    {name: "query", type: "text", title: "Query"},
                    {name: "answer", type: "text", title: "Answer"},
                    {
                        name: "recommended_test",
                        type: "text",
                        width: 100,
                        css: "text-center",
                        title: "Recommended Test",
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
                        title: "Change Test",
                        validate: {
                            validator: "required",
                            message: function () {
                                return "Please Enter a Test Name";
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
            let url = "{{ route('admin.assess.test.recommend.modify.page',[":queryId",":answer"]) }}";
            url = url.replace(":queryId", item.query_id).replace(":answer", item.answer);
            return "<a href=\"" + url + "\" target=\"_blank\">Modify Test</a>";
        }

        // For Reloading Data in case of any updates
        $(window).focus(function () {
            $("#jsGrid-basic").jsGrid("loadData");
        });
    </script>
@endsection