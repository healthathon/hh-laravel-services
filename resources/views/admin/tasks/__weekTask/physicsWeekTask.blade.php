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
        {{--<div class="col p-md-0">--}}
        {{--<h4>Hello, <span>Welcome here</span></h4>--}}
        {{--</div>--}}
        {{--<div class="col p-md-0">--}}
        {{--<ol class="breadcrumb">--}}
        {{--<li class="breadcrumb-item"><a href="javascript:void(0)">Layout</a>--}}
        {{--</li>--}}
        {{--<li class="breadcrumb-item active">Blank</li>--}}
        {{--</ol>--}}
        {{--</div>--}}
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Physical Week Tasks</h4>
                    <div id="jsGrid-basic"></div>
                </div>
            </div>
        </div>
        <!-- <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Basic</h4>
                    <div id="jsGrid"></div>
                </div>
            </div>
        </div> -->
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
                // filtering: true,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,

                // data:data,
                controller: {
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            url: "{{url('/admin/task/getPhysicsWeekTask/'.$taskBank_id)}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    insertItem: function (updateItem) {
                        var formData = new FormData();
                        if (updateItem.badge != null)
                            formData.append("badge", updateItem.badge, updateItem.badge.name);
                        formData.append("ID", updateItem.ID);
                        formData.append("taskBank_id", "{{$taskBank_id}}");
                        formData.append("week", updateItem.week);
                        formData.append("day1_title", updateItem.day1_title);
                        formData.append("day1_message", updateItem.day1_title);
                        formData.append("day2_title", updateItem.day2_title);
                        formData.append("day2_message", updateItem.day2_title);
                        formData.append("day3_title", updateItem.day3_title);
                        formData.append("day3_message", updateItem.day3_title);
                        formData.append("day4_title", updateItem.day4_title);
                        formData.append("day4_message", updateItem.day4_title);
                        formData.append("day5_title", updateItem.day5_title);
                        formData.append("day5_message", updateItem.day5_title);
                        formData.append("day6_title", updateItem.day6_title);
                        formData.append("day6_message", updateItem.day6_title);
                        formData.append("day7_title", updateItem.day7_title);
                        formData.append("day7_message", updateItem.day7_title);

                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/insertPhysicsWeekTask')}}",
                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                        }).done(function (result) {
                            console.log(result);
                        });
                    },
                    updateItem: function (updateItem) {
                        var formData = new FormData();

                        // if (updateItem.badge != null)
                        //     formData.append("badge", updateItem.badge, updateItem.badge.name);
                        formData.append("ID", updateItem.ID);
                        formData.append("week", updateItem.week);
                        formData.append("day1_title", updateItem.day1_title);
                        formData.append("day1_message", updateItem.day1_title);
                        formData.append("day2_title", updateItem.day2_title);
                        formData.append("day2_message", updateItem.day2_title);
                        formData.append("day3_title", updateItem.day3_title);
                        formData.append("day3_message", updateItem.day3_title);
                        formData.append("day4_title", updateItem.day4_title);
                        formData.append("day4_message", updateItem.day4_title);
                        formData.append("day5_title", updateItem.day5_title);
                        formData.append("day5_message", updateItem.day5_title);
                        formData.append("day6_title", updateItem.day6_title);
                        formData.append("day6_message", updateItem.day6_title);
                        formData.append("day7_title", updateItem.day7_title);
                        formData.append("day7_message", updateItem.day7_title);
                        formData.append("taskBank_id", "{{$taskBank_id}}");

                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/updatePhysicsWeekTask')}}",
                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType: 'json',


                        }).done(function (result) {
                            console.log(result);
                        });
                    },
                    deleteItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/deletePhysicsWeekTask')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            // console.log(result);
                        });
                    }
                },

                fields: [
                    {
                        name: "week",
                        type: "number",
                        width: 50,
                        validate: "required",
                        title: "Week",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day1_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Mon",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day1_message",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Mon Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day2_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Tue",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day2_message",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Tue Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day3_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Wed",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day3_message",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Wed Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day4_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Thu",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day4_message",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Thu Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day5_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Fri",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day5_message",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Fri Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day6_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Sat",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day6_message",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Sat Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day7_title",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Sun",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day7_message",
                        type: "text",
                        width: 150,
                        validate: "required",
                        title: "Sun Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "badge",
                        itemTemplate: function (val, item) {
                            console.log(val);
                            return $("<img>").attr("src", val).css({height: 60, width: 60});
                        },
                        insertTemplate: function () {
                            var insertControl = this.insertControl = $("<input>").prop("type", "file");
                            return insertControl;
                        },
                        insertValue: function () {
                            return this.insertControl[0].files[0];
                        },
                        editTemplate: function (val, item) {
                            var insertControl = this.insertControl = $("<input>").prop("type", "file");
                            return insertControl;
                        },
                        editValue: function () {
                            return this.insertControl[0].files[0];
                        },

                        align: "center",
                        width: 100
                    },
                    {name: "ID", type: "hidden", css: 'hide'},
                    {name: "week", type: "hidden", css: 'hide'},
                    {
                        type: "control",
                    }
                ]
            });
            // $("#jsGrid-basic").jsGrid("option", "filtering", false);
        });
    </script>
@endsection