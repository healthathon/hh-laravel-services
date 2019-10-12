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
                    <h4 class="card-title mb-4">Edit LifeStyle Week Tasks</h4>
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
                            url: "{{url('/admin/task/getMentalWeekTask/'.$taskBank_id)}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    insertItem:function(updateItem){

                        var formData = new FormData();
                        if (updateItem.badge!=null)
                            formData.append("badge",updateItem.badge,updateItem.badge.name);

                        formData.append("ID",updateItem.ID);
                        formData.append("week",updateItem.week);
                        formData.append("week_title",updateItem.week_title);

                        formData.append("week_detail",updateItem.week_detail);
                        formData.append("x",updateItem.x);
                        formData.append("y",updateItem.y);
                        formData.append("taskBank_id","{{$taskBank_id}}");

                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/insertMentalWeekTask')}}",

                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType:'json',

                        }).done(function (result) {
                            console.log(result);
                        });
                    },
                    updateItem:function (updateItem) {

                        var formData = new FormData();
                        if (updateItem.badge!=null)
                            formData.append("badge",updateItem.badge,updateItem.badge.name);

                        formData.append("ID",updateItem.ID);
                        formData.append("week",updateItem.week);
                        formData.append("week_title",updateItem.week_title);

                        formData.append("week_detail",updateItem.week_detail);
                        formData.append("x",updateItem.x);
                        formData.append("y",updateItem.y);
                        formData.append("taskBank_id","{{$taskBank_id}}");

                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/updateMentalWeekTask')}}",
                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType:'json',
                        }).done(function (result) {
                            // console.log(result);
                        });
                    },
                    deleteItem:function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('/admin/task/deleteMentalWeekTask')}}",
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
                    { name: "week_title", type: "text", width: 150, validate: "required",title:"Week Task",css:"text-center",align: "center"},
                    { name: "y", type: "number", width: 100, validate: "required",title:"Y, %",css:"text-center",align: "center"},
                    { name: "x", type: "number", width: 100, validate: "required",title:"X",css:"text-center",align: "center"},

                    {
                        name:"badge",
                        itemTemplate:function (val,item) {
                            return $("<img>").attr("src",val).css({height:60,width:60});
                        },
                        insertTemplate:function () {
                            var insertControl=this.insertControl=$("<input>").prop("type","file");
                            return insertControl;
                        },
                        insertValue:function () {
                            return this.insertControl[0].files[0];
                        },
                        editTemplate:function(val,item){
                            var insertControl=this.insertControl=$("<input>").prop("type","file");
                            return insertControl;
                        },
                        editValue:function () {
                            return this.insertControl[0].files[0];
                        },

                        align:"center",
                        width:100
                    },

                    { name: "week_detail", type: "text", width: 150,title:"Week Detail",css:"text-center",align: "center"},
                    { name:"ID" ,type: "hidden", css: 'hide'},
                    {
                        type: "control",
                    }
                ]
            });

        });
    </script>
@endsection