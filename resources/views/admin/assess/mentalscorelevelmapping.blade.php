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
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Mental Score Level Mapping</h4>
                    <div class="alert hide messageBox">
                        Test Message
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
                height: "800px",

                editing: true,
                filtering: true,
                deleting: true,
                inserting: true,
                searching: true,
                sorting: false,
                paging: true,
                // filtering: true,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,


                // data:data,
                controller: {
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            url: "{{route("admin.assess.score-level-map.info")}}",
                        }).done(function (result) {
                        })
                    },
                    insertItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{route("admin.assess.score-level-map.insert")}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showResponseMessage(result);
                        });
                    },
                    updateItem: function (item) {
                        let url = "{{ route("admin.assess.score-level-map.update",":id") }}";
                        url = url.replace(":id", item.id);
                        return $.ajax({
                            method: "put",
                            url: url,
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showResponseMessage(result);
                        });
                    },
                    deleteItem: function (item) {
                        let url = "{{ route("admin.assess.score-level-map.delete",":id") }}";
                        url = url.replace(":id", item.id);
                        return $.ajax({
                            method: "delete",
                            url: url,
                        }).done(function (result) {
                            showResponseMessage(result);
                        });
                    }
                },

                fields: [
                    {
                        name: "tag",
                        type: "text",
                        width: 100,
                        validate: "required",
                        title: "Tag",
                        css: "text-center",
                        filtering: true,
                        editing: false
                    },
                    {
                        name: "state",
                        type: "text",
                        width: 100,
                        validate: "required",
                        title: "State",
                        css: "text-center",
                        filtering: true,
                    },
                    {
                        name: "level",
                        type: "number",
                        width: 100,
                        validate: "required",
                        title: "Level",
                        css: "text-center",
                        filtering: true,
                    },
                    {
                        name: "score", type: "number", width: 100, css: "text-center", title: "Score",
                        filtering: true,
                    },
                    {name: "ID", type: "hidden", css: 'hide'},
                    {type: "control"}
                ]
            });
        });

        function showResponseMessage(response) {
            let messageBoxRef = $(".messageBox");
            messageBoxRef.removeClass("hide");
            if ("error" in response)
                messageBoxRef.removeClass("alert-success").addClass("alert-danger").text(response["error"]);
            else
                messageBoxRef.removeClass("alert-danger").addClass("alert-success").text(response["data"]);
            $("#jsGrid-basic").jsGrid("loadData");
        }
    </script>
@endsection