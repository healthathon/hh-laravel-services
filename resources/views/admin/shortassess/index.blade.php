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
                    <h4 class="card-title mb-4">SHA Questions</h4>
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
                inserting: true,
                sorting: false,
                paging: false,
                filtering: false,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,
                search: false,
                deleteConfirm: "You are going to delete this question, are you sure?",
                controller: {
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            url: "{{route('admin.sha.questions')}}",
                        }).done(function (result) {
                            console.log(result);
                        })
                    },
                    insertItem: function (item) {
                        let url = "{{route('admin.sha.insert')}}";
                        return $.ajax({
                            method: "post",
                            url: url,
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showMessage(result);
                            jsGridRef.jsGrid("loadData");
                        });
                    },
                    deleteItem: function (item) {
                        let url = "{{route('admin.sha.delete',':id')}}";
                        url = url.replace(':id', item['id']);
                        return $.ajax({
                            method: "delete",
                            url: url,
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showMessage(result);
                            jsGridRef.jsGrid("loadData");
                        });
                    },
                    updateItem: function (item) {
                        let url = "{{route('admin.sha.update',':id')}}";
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
                        title: "Sr. Id",
                        inserting: false,
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        }
                    },
                    {
                        name: "header",
                        type: "text",
                        width: 120,
                        validate: "required",
                        css: "text-center",
                        title: "Title",
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        },
                        insertTemplate: function () {
                            return "<input type='text' id='headerTitle'/>";
                        },
                        insertValue: function () {
                            return $("#headerTitle").val();
                        }
                    },
                    {
                        name: "question",
                        type: "text",
                        width: 120,
                        validate: "required",
                        css: "text-center",
                        title: "Question",
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        }
                    },
                    {
                        name: "answers",
                        type: "text",
                        width: 180,
                        validate: "required",
                        css: "text-center",
                        title: "Answers",
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        },
                        editTemplate: function (item) {
                            initInputTags();
                            return '<input type="text" id="answers" class="form-control input-sm" value="' + item + '" data-role="tagsinput"/>';
                        },
                        editValue: function () {
                            return $("#answers").val();
                        },
                        insertTemplate: function () {
                            initInputTags();
                            return '<input type="text" id="answersInsert" class="form-control input-sm" data-role="tagsinput"/>';
                        },
                        insertValue: function () {
                            return $("#answersInsert").val();
                        }
                    },
                    {
                        name: "score",
                        type: "text",
                        width: 100,
                        validate: "required",
                        css: "text-center",
                        title: "Scores",
                        itemTemplate: function (item) {
                            return showBlackColorText(item);
                        },
                        editTemplate: function (item) {
                            return '<input type="text" id="scores" class="form-control input-sm" value="' + item + '"/>';
                        },
                        editValue: function () {
                            return $("#scores").val();
                        },
                        insertTemplate: function () {
                            return '<input type="text" id="scoresInsert" class="form-control input-sm"/>';
                        },
                        insertValue: function () {
                            return $("#scoresInsert").val();
                        }
                    },
                    {
                        name: "is_scoreable",
                        type: "select",
                        width: 100,
                        validate: "required",
                        css: "text-center",
                        title: "Scoreable Field",
                        itemTemplate: function (item) {
                            let eleRef = "<select>";
                            eleRef += "<option value='1'" + (item === 1 ? "selected" : "") + ">Yes</option>";
                            eleRef += "<option value='0'" + (item === 0 ? "selected" : "") + ">No</option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        editTemplate: function (item) {
                            let eleRef = "<select id='scorebaleEditValue'>";
                            eleRef += "<option value='1'" + (item === 1 ? "selected" : "") + ">Yes</option>";
                            eleRef += "<option value='0'" + (item === 0 ? "selected" : "") + ">No</option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        editValue: function () {
                            return $("#scorebaleEditValue").val();
                        }
                    },
                    {
                        name: "multiple",
                        type: "text",
                        width: 70,
                        validate: "required",
                        css: "text-center",
                        title: "Multiple",
                        itemTemplate: function (item) {
                            if (item) {
                                return "<b style='color:black;'>true</b>";
                            } else {
                                return "<b style='color:black;'>false</b>";
                            }
                        },
                        editTemplate: function (item) {
                            let element = "<select id='multiple'>";
                            element += "<option value='1'" + (item === 1 ? 'selected' : '') + ">True</option>";
                            element += "<option value='0'" + (item === 0 ? 'selected' : '') + ">False</option>";
                            element += "</select>";
                            return element;
                        },
                        editValue: function () {
                            return $("#multiple").val();
                        },
                        insertTemplate: function () {
                            let element = "<select id='multipleInsert'>";
                            element += "<option value='1'>True</option>";
                            element += "<option value='0'>False</option>";
                            element += "</select>";
                            return element;
                        },
                        insertValue: function () {
                            return $("#multipleInsert").val();
                        }
                    },
                    {type: "control", deleteButton: true}
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