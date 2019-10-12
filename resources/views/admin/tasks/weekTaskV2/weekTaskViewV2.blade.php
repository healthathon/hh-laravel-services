@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        .error, .messageBox, .hide, #addWeekTaskForm {
            display: none;
        }

        .show-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .text-green {
            color: green;
        }

        * {
            color: black;
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
                    <h4 class="card-title mb-4">Edit {{ $regimenName }}
                        Week Tasks [ Regimen Code : <i class="text-green">{{ $regimenCode }}</i>]
                        <button type="button" class="btn btn-success" title="Add" id="addWeekTaskBtn">+</button>
                        <button type="button" class="btn btn-primary" title="View" id="viewAllWeekTaskBtn">
                            <i class="mdi mdi-eye text-white"></i>
                        </button>
                    </h4>
                    <div class="alert messageBox">
                    </div>
                    <div id="addWeekTaskForm">
                        @include("admin.tasks.weekTaskV2.addWeeklyTaskPage")
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            'use strict';
            $("#jsGrid-basic").jsGrid({
                width: "100%",
                height: "800px",
                editing: true,
                deleting: true,
                inserting: true,
                sorting: false,
                paging: true,
                filtering: false,
                pageSize: 7,
                pageButtonCount: 5,
                autoload: true,
                controller: {
                    loadData: function () {
                        return $.ajax({
                            method: "GET",
                            url: "{{ route('admin.regimen.week_details_info',["regimenCode" => $regimenCode]) }}",
                        }).done(function (result) {
                        })
                    },
                    insertItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{route('admin.regimen.weekly.insert')}}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                item
                            },
                        }).done(function (response) {
                            showResponseBox(response);
                        });
                    },
                    updateItem: function (item) {
                        let url = "{{route('admin.regimen.week.info.update',['regimenCode' => $regimenCode,':weekNo'])}}";
                        url = url.replace(":weekNo", item.week);
                        return $.ajax({
                            method: "patch",
                            url: url,
                            data: {
                                item,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                showResponseBox(response);
                                $("#jsGrid-basic").jsGrid("loadData");
                            },
                            error: function (response) {
                                showResponseBox(response);
                            }
                        });
                    },
                    deleteItem: function (item) {
                        let url = "{{route("admin.regimen.weekly.delete",[":week",":code"])}}";
                        url = url.replace(":week", item.week).replace(":code", item.taskBank_id);
                        return $.ajax({
                            method: "delete",
                            url: url,
                            success: function (response) {
                                showResponseBox(response);
                            }
                        });
                    }
                },
                fields: [
                    {
                        name: "week",
                        editing: false,
                        type: "number",
                        width: 50,
                        title: "Week",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "advise", type: "text", width: 150, css: "text-center", title: "Advise", align: "center",
                        itemTemplate: function (item, data) {
                            let url = "{{ route("admin.regimen.weekly.advise.page",[":week",":code"]) }}";
                            url = url.replace(":week", data.week).replace(":code", data.taskBank_id);
                            return "<a href='" + url + "' target='_blank'>View Advise</a>";
                        }
                    },
                    {
                        name: "day1_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Mon Task",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day1_message",
                        type: "textarea",
                        width: 150,
                        title: "Mon Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day1_badge",
                        type: "text",
                        width: 150,
                        title: "Mon Badge",
                        css: "text-center",
                        align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "day2_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Tue Task",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day2_message",
                        type: "textarea",
                        width: 150,
                        title: "Tue Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day2_badge",
                        type: "text",
                        width: 150,
                        title: "Tue Badge",
                        css: "text-center",
                        align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "day3_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Wed Task",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day3_message",
                        type: "textarea",
                        width: 150,
                        title: "Wed Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day3_badge",
                        type: "text",
                        width: 150,
                        title: "Wed Badge",
                        css: "text-center",
                        align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "day4_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Thu Task",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day4_message",
                        type: "textarea",
                        width: 150,
                        title: "Thu Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day4_badge",
                        type: "text",
                        width: 150,
                        title: "Thu Badge",
                        css: "text-center",
                        align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "day5_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Fri Task",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day5_message",
                        type: "textarea",
                        width: 150,
                        title: "Fri Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day5_badge",
                        type: "text",
                        width: 150,
                        title: "Fri Badge",
                        css: "text-center",
                        align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "day6_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Sat Task",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day6_message",
                        type: "textarea",
                        width: 150,
                        title: "Sat Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day6_badge",
                        type: "text",
                        width: 150,
                        title: "Sat Badge",
                        css: "text-center",
                        align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "day7_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Sun Task",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day7_message",
                        type: "textarea",
                        width: 150,
                        title: "Sun Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day7_badge", type: "text", width: 150,
                        title: "Sun Badge", css: "text-center", align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "image",
                        type: "text",
                        width: 150,
                        title: "Week Badge",
                        itemTemplate: function (val, item) {
                            return $("<img>").attr("src", val).css({height: 60, width: 60});
                        },
                        align: "center",
                    },
                    {
                        name: "taskBank_id", type: 'text', css: 'hide',
                        insertValue: function () {
                            return "{{ $regimenCode }}";
                        }
                    },
                    {name: "ID", type: "hidden", css: 'hide', inserting: false, editing: false},
                    {name: "week", type: "text", css: 'hide', inserting: true, editing: false},
                    {type: "control", editButton: true, deleteButton: true},
                ],
                shrinkToFit: false,
                forceFit: true,
            });
        });

        function showResponseBox(response) {
            let messageBoxRef = $(".messageBox");
            messageBoxRef.css("display", "block");
            if ("error" in response) {
                messageBoxRef.removeClass("alert alert-success").addClass("alert alert-danger");
                messageBoxRef.text(response["error"]);
            } else {
                messageBoxRef.removeClass("alert alert-danger").addClass("alert alert-success");
                messageBoxRef.text(response["data"]);
            }
        }

        $("document").ready(function () {
            $("#addWeekTaskBtn").on('click', function (e) {
                e.preventDefault();
                $("#addWeekTaskForm").css("display", "block");
                $("#jsGrid-basic").css("display", "none");
            });
            $("#viewAllWeekTaskBtn").on('click', function (e) {
                e.preventDefault();
                let jsGridRef = $("#jsGrid-basic");
                jsGridRef.css("display", "block");
                $("#addWeekTaskForm").css("display", "none");
                jsGridRef.jsGrid("loadData");
            });

            $("#saveData").on('click', function (e) {
                e.preventDefault();
                let weekDetails = {};
                weekDetails["week"] = $("#week").val();
                for (i = 1; i <= 7; i++) {
                    weekDetails["day" + i + "_title"] = $("#day" + i + "_title").val();
                    weekDetails["day" + i + "_badge"] = $("#day" + i + "_badge").val();
                    weekDetails["day" + i + "_message"] = $("#day" + i + "_message").val();
                }
                weekDetails["image"] = $("#badge").val();
                weekDetails["advise"] = $("#advise").val();
                weekDetails["taskBank_id"] = "{{ $regimenCode }}";
                $.ajax({
                    method: "post",
                    url: "{{ route("admin.regimen.weekly.insert") }}",
                    data: {
                        _token: "{{  csrf_token() }}",
                        weekDetails
                    },
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        showResponseBox(response);
                    }
                });
            });
        });
    </script>
@endsection