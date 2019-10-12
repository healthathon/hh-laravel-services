@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        .hide{
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
                    <h4 class="card-title mb-4">Edit Assess Questions</h4>
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
        $(function($){
            'use strict';

            $("#jsGrid-basic").jsGrid({
                width: "100%",
                height: "800px",

                editing: true,
                deleting:true,
                inserting:true,
                sorting: false,
                paging: true,
                // filtering: true,
                pageSize:10,
                pageButtonCount: 5,
                autoload: true,

                // data:data,
                controller: {
                    loadData: function(filter){
                        return $.ajax({
                            method: "GET",
                            url: "{{url('/admin/task/getWeekTask/'.$taskBank_id)}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    insertItem:function(item){
                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/insertWeekTask')}}",

                            data: {
                                item,
                                taskBank_id:"{{$taskBank_id}}",
                                _token : $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            console.log(result);
                        });
                    },
                    updateItem:function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/updateWeekTask')}}",
                            data: {
                                item,
                                taskBank_id:"{{$taskBank_id}}",
                                _token : $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            // console.log(result);
                        });
                    },
                    deleteItem:function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/deleteWeekTask')}}",
                            data: {
                                item,
                                _token : $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            // console.log(result);
                        });
                    }
                },

                fields: [
                    { name: "week", type: "number", width: 50, validate: "required",title:"Week",css:"text-center",align: "center"},
                    { name: "day1_title", type: "text", width: 150, validate: "required",title:"Mon",css:"text-center",align: "center"},
                    { name: "day2_title", type: "text", width: 150, validate: "required",title:"Tue",css:"text-center",align: "center"},
                    { name: "day3_title", type: "text", width: 150, validate: "required",title:"Wed",css:"text-center",align: "center"},
                    { name: "day4_title", type: "text", width: 150, validate: "required",title:"Thu",css:"text-center",align: "center"},
                    { name: "day5_title", type: "text", width: 150, validate: "required",title:"Fri",css:"text-center",align: "center"},
                    { name: "day6_title", type: "text", width: 150, validate: "required",title:"Sat",css:"text-center",align: "center"},
                    { name: "day7_title", type: "text", width: 150, validate: "required",title:"Sun",css:"text-center",align: "center"},
                    { name: "week_detail", type: "text", width: 300,title:"Week Detail",css:"text-center",align: "center"},
                    { name:"ID" ,type: "hidden", css: 'hide'},
                    {
                        type: "control",
                    }
                ]
            });
            // $("#jsGrid-basic").jsGrid("option", "filtering", false);
        });
    </script>
@endsection