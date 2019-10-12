@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/css/bootstrap-tagsinput.css')}}">
    <style>
        .hide {
            display: none;
        }

        .bootstrap-tagsinput {
            border: transparent !important;
            display: grid !important;
            box-shadow: none !important;
        }

        .bootstrap-tagsinput .tag {
            font-size: small !important;
        }

        .bootstrap-tagsinput input[type=text] {
            max-width: 100% !important;
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
                <li class="breadcrumb-item active">Short Health Assessment</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Level Restrictions</h4>
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
    <script src="{{asset('admin/js/bootstrap-tagsinput.min.js')}}"></script>
    <script>
        $(function ($) {
            'use strict';
            const jsGridRef = $("#jsGrid-basic");
            jsGridRef.jsGrid({
                width: "100%",
                height: "400px",
                editing: true,
                deleting: false,
                inserting: false,
                sorting: true,
                paging: true,
                filtering: true,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,
                search: true,
                deleteConfirm: "You are going to delete this question, are you sure?",
                controller: {
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            url: "{{route('admin.sha.level.restriction.info')}}",
                        }).done(function (result) {
                        })
                    },
                    deleteItem: function (item) {
                        let url = "{{route('admin.sha.level.restriction.delete',':id')}}";
                        url = url.replace(':id', item.id);
                        return $.ajax({
                            method: "delete",
                            url: url,
                        }).done(function (result) {
                            showMessage(result);
                            jsGridRef.jsGrid("loadData");
                        });
                    },
                    updateItem: function (item) {
                        let url = "{{route('admin.sha.level.restriction.update',':id')}}";
                        url = url.replace(':id', item['id']);
                        return $.ajax({
                            method: "put",
                            url: url,
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showMessage(result);
                            jsGridRef.jsGrid("loadData");
                        });
                    }
                },
                fields: [
                    {
                        name: "id",
                        type: "read-only",
                        width: 40,
                        css: "text-center",
                        title: "Id",
                        inserting: false,
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        }
                    },
                    {
                        name: "answer",
                        type: "text",
                        editing: false,
                        width: 120,
                        validate: "required",
                        css: "text-center",
                        title: "SHA Answer",
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        }
                    },
                    {
                        name: "restriction_level",
                        type: "number",
                        width: 60,
                        validate: "required",
                        css: "text-center",
                        title: "Level Restriction",
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        }
                    },
                    {type: "control", deleteButton: true, editButton: true}
                ]
            });
        });

        function initInputTags() {
            $(function ($) {
                $("input[data-role=tagsinput]").tagsinput();
            });
        }

        function showBlackColorText(item) {
            return "<b style='color:black;'>" + item + "</b>";
        }

        function showMessage(response) {
            let updateMessageRef = $("#updateMessage");
            updateMessageRef.css("display", "block");
            if ("error" in response)
                updateMessageRef.removeClass("alert-success").addClass("alert-danger").text(response["error"]);
            else {
                updateMessageRef.removeClass("alert-danger").addClass("alert-success").text(response["data"]);
            }
        }
    </script>
@endsection