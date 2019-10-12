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
    <script>
        var categories=<?php echo $result?>;
    </script>
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
                    <h4 class="card-title mb-4">Edit Score Interpolation Rule</h4>
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
                            url: "{{url('admin/assess/get_interp_list')}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    insertItem:function(item){
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/assess/insert_interp')}}",
                            data: {
                                item,
                                _token : $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            // item['ID']=result;
                        });
                    },
                    updateItem:function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/assess/update_interp')}}",
                            data: {
                                item,
                                _token : $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            // console.log(result);
                        });
                    },
                    deleteItem:function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/assess/delete_interp')}}",
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
                    { name: "category1", type: "select", width: 200, validate: "required",title:categories[0]['category_name'],css:"text-center",
                        items: [
                            { Name: null, Id: 0 },
                            { Name: "Excellent", Id:1},
                            { Name: "Good", Id: 2},
                            { Name: "Bad", Id: 3},
                        ],
                        valueField: "Id",
                        textField: "Name",
                        filtering:true,

                    },
                    { name: "category2", type: "select", width: 200, validate: "required",title:categories[1]['category_name'],css:"text-center",
                        items: [
                            { Name: null, Id: 0 },
                            { Name: "Excellent", Id:1},
                            { Name: "Good", Id: 2},
                            { Name: "Bad", Id: 3},
                        ],
                        valueField: "Id",
                        textField: "Name",
                        filtering:true,
                    },
                    { name: "category3", type: "select", width: 200, validate: "required",title:categories[2]['category_name'],css:"text-center",
                        items: [
                            { Name: null, Id: 0 },
                            { Name: "Excellent", Id:1},
                            { Name: "Good", Id: 2},
                            { Name: "Bad", Id: 3},
                        ],
                        valueField: "Id",
                        textField: "Name",
                        filtering:true,
                    },
                    { name: "level", type: "select", width: 200, css:"text-center",title:"Level",
                        items: [
                            { Name: "1", Id:1},
                            { Name: "2", Id: 2},
                            { Name: "3", Id: 3},
                            { Name: "4", Id: 4},
                        ],
                        valueField: "Id",
                        textField: "Name",
                        filtering:true,
                    },
                    { name:"ID" ,type: "hidden", css: 'hide'},
                    { type: "control"}
                ]
            });
            $("#jsGrid-basic").jsGrid("option", "filtering", false);

        });
    </script>
@endsection