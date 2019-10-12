@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        .hide {
            display: none;
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
                    <h4 class="card-title mb-4">Recommended Regimen on Assessment Answers</h4>
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
                            url: "{{route('admin.assess.regimen.recommend.get')}}",
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
                        let url = "{{ route("admin.assess.query.answers.restriction.level.update",[":id"]) }}";
                        url = url.replace(":id", item.id);
                        return processRequest("patch", url, item, "Recommendation Updated");
                    }
                },
                fields: [
                    {name: "id", type: "number", css: 'hide'},
                    {name: "query", type: "read-only", title: "Query"},
                    {name: "answer", type: "read-only", title: "Answer"},
                    {
                        name: "restricted_level",
                        type: "number",
                        title: "Level Restriction",
                        width: 70,
                        css: "text-center"
                    },
                    {
                        name: "recommended_regimen",
                        type: "read-only",
                        width: 100,
                        css: "text-center ellipsis",
                        title: "Recommended Regimen"
                    },
                    {
                        name: "change_test",
                        type: "read-only",
                        width: 100,
                        css: "text-center",
                        title: "Update Recommend",
                        itemTemplate: function (value, item) {
                            const $link = show(item);
                            return $("<div>").append($link);
                        }
                    }
                ],
                confirmDeleting: true,
                deleteConfirm: "Are you sure?",
                noDataContent: "No Recommendation Found"
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
                let alertRef = $(".alert");
                alertRef.removeClass("hide");
                "error" in result ? alertRef.addClass("alert-danger").text(result["error"]) :
                    alertRef.addClass("alert-success").text(result["data"]);
                $("#jsGrid-basic").jsGrid("loadData");
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
        // $(window).focus(function () {
        //     $("#jsGrid-basic").jsGrid("loadData");
        // });
    </script>
@endsection