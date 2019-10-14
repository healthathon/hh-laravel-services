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
                <div class="card-header">
                    <h4 class="card-title mb-4">View Labs Tests</h4>
                </div>
                <div class="card-body">
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
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            dataType: "json",
                            url: "{{url('admin/assess/fetch/tests')}}",
                        }).done(function (result) {
                        })
                    },
                    deleteItem: function (item) {
                        let url = "{{ url('admin/labs/test/:id/delete')}}";
                        url = url.replace(':id', item.test_id);
                        // Common method can be use
                        $.ajax({
                            method: "delete",
                            url: url,
                        }).done(function (result) {
                            let alertRef = $(".alert");
                            if (result.status) {
                                alertRef.addClass("alert-success")
                                    .removeClass("hide")
                                    .text('Test Deleted');
                            } else {
                                alertRef.addClass("alert-danger")
                                    .removeClass("hide")
                                    .text(result.message);
                            }
                        });
                    },
                    updateItem: function (item) {
                        return processRequest("put", "{{url('admin/assess/1/update/test')}}", item, "Test Updated Successfully");
                    }
                },
                fields: [
                    {name: "id", type: "number", css: 'hide'},
                    {name: "test_id", type: "number", css: 'hide'},
                    {name: "test_name", type: "text", width: 100, css: "text-center", title: "Test Name"},
                    {name: "lab.name", type: "text", width: 100, css: "text-center", title: "Lab Name"},
                    {name: "profile", type: "text", width: 100, css: "text-center", title: "Profile Name"},
                    {name: "abbr", type: "text", width: 100, css: "text-center", title: "Abbreviation"},
                    {name: "price", type: "number", width: 100, css: "text-center", title: "Cost"},
                    // {type: "control", deleteButton: false, search: true},
                    {
                        name: 'edit', type: 'text', width: 100, title: "Modify Test",
                        itemTemplate: function (value, item) {
                            // Redirect to modify page
                            const url = window.location.origin + "/admin/labs/show/test/" + item.test_id;
                            const $link = "<a href=\"" + url + "\" target=\"_blank\">Modify Test</a>";
                            return $("<div>").append($link);
                        }
                    }, {
                        type: 'control', editButton: false, deleteButton: true
                    }
                ],
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