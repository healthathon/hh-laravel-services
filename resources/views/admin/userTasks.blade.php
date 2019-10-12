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
        <div class="col p-md-0">
            {{--<h4>Hello, <span>Welcome here</span></h4>--}}
        </div>
        <div class="col p-md-0">
            {{--<ol class="breadcrumb">--}}
            {{--<li class="breadcrumb-item"><a href="javascript:void(0)">Layout</a>--}}
            {{--</li>--}}
            {{--<li class="breadcrumb-item active">Blank</li>--}}
            {{--</ol>--}}
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Users Tasks Information</h4>
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
                height: "400px",

                editing: false,
                deleting: false,
                inserting: false,
                sorting: true,
                paging: true,
                filtering: false,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,
                search: true,


                // data:data,
                controller: {
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            url: "{{url('admin/get_users_task')}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    updateItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/assess/update_category')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            console.log(result);

                        });

                    }
                },

                fields: [
                    {
                        name: "userId",
                        type: "read-only",
                        width: 70,
                        validate: "required",
                        css: "text-center",
                        title: "User Id"
                    },
                    {
                        name: "userName",
                        type: "read-only",
                        width: 70,
                        validate: "required",
                        css: "text-center",
                        title: "User Name"
                    },
                    {
                        name: "physical",
                        type: "read-only",
                        width: 100,
                        validate: "required",
                        css: "text-center",
                        title: "Physical Task",
                        itemTemplate: function (item) {
                            return showFormattedData(item);
                        }
                    },
                    {
                        name: "mental",
                        type: "read-only",
                        width: 100,
                        validate: "required",
                        css: "text-center",
                        title: "Mental Task",
                        itemTemplate: function (item) {
                            return showFormattedData(item);
                        }
                    },
                    {
                        name: "lifestyle",
                        type: "read-only",
                        width: 100,
                        validate: "required",
                        css: "text-center",
                        title: "LifeStyle Task",
                        itemTemplate: function (item) {
                            return showFormattedData(item);
                        }
                    },
                    {
                        name: "nutrition",
                        type: "read-only",
                        width: 100,
                        validate: "required",
                        css: "text-center",
                        title: "Nutrition Task",
                        itemTemplate: function (item) {
                            return showFormattedData(item);
                        }
                    },
                    {name: "ID", type: "hidden", css: 'hide'},
                ]
            });
        });

        function showFormattedData(item) {
            if (item.length === 0) {
                return "<p>Not yet registered</p>";
            }
            return "<p style='word-break: break-all;'>" + item + "</p>";
        }
    </script>
@endsection