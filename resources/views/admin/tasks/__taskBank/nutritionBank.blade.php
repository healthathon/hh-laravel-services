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
                    <h4 class="card-title mb-4">Edit Nutrition Task Banks</h4>
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
            'use strict';

            $("#jsGrid-basic").jsGrid({
                width: "100%",
                height: "900px",

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
                            url: "{{url('admin/task/taskBank/getNutritionBank')}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    insertItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/task/taskBank/insertNutritionBank')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            // item['ID']=result;
                        });
                    },
                    updateItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/task/taskBank/updateNutritionBank')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            // console.log(result);
                        });
                    },
                    deleteItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/task/taskBank/deleteNutritionBank')}}",
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
                        name: "task_name",
                        type: "text",
                        width: 200,
                        validate: "required",
                        title: "TaskBank Name",
                        css: "text-center",
                        align: "center"
                    },

                    {name: "ID", type: "hidden", css: 'hide'},
                    {name: "title", type: "text", width: 200, css: "text-center", title: "Title", align: "center"},
                    {
                        name: "regimen_badge",
                        type: "file",
                        width: 150,
                        css: "text-center",
                        title: "Regimen Badge",
                        align: "center"
                    },
                    {
                        name: "view_badge",
                        type: "image",
                        width: 150,
                        css: "text-center",
                        title: "View Badge",
                        align: "center",
                        itemTemplate: function (value, item) {
                            const $text = $("<img>").attr('src', value).attr('height', 80).attr('width', 80);
                            const $p = $("<p id='imgLoaded_" + item.ID + "'>").append("Loaded");
                            return $("<div>").append($text).append($p);
                        }
                    },
                    {
                        name: "week_task", width: 200, css: "text-center", title: "View Weekly Tasks", align: "center",
                        itemTemplate: function (value, item) {
                            var $text = $("<p>").text(item.MyField);
                            var $link = $("<a>").attr("href", "{{url('/admin/task/showNutritionWeekTask')}}" + "/" + item.ID).text("View Weekly Tasks");
                            return $("<div>").append($text).append($link);
                        }
                    },
                    {
                        type: "control",
                    }
                ]
            });
            $("#jsGrid-basic").jsGrid("option", "filtering", false);
        });

        function showContent(e, regimenId) {
            var formData = new FormData();
            var file = e.files[0];
            formData.append("regimenId", regimenId);
            formData.append("_token", "{{ csrf_token() }}");
            formData.append("fileData", file);
            $.ajax({
                method: "POST",
                url: "{{url('admin/regimen/upload-regimen-image')}}",
                processData: false,
                cache: false,
                contentType: false,
                data: formData,
                beforeSend: function () {
                    $("p#imgLoaded_" + regimenId).text("Uploading Image.....");
                },
                success: function (response) {
                    $("p#imgLoaded_" + regimenId).text("Image Uploaded.. Refreshing in 2 seconds..");
                    setInterval(function () {
                        location.reload();
                    }, 2000);
                },
                error: function (response) {
                    console.log(response);
                }
            })
        }
    </script>
@endsection