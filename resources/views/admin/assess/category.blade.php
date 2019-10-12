@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        .hide{
            display:none;
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
                    <h4 class="card-title mb-4">Edit Assess Categories</h4>
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
                height: "400px",

                editing: true,
                deleting:false,
                inserting:false,
                sorting: true,
                paging: true,
                filtering: false,
                pageSize:10,
                pageButtonCount: 5,
                autoload: true,
                search:false,


                // data:data,
                controller: {
                    loadData: function(filter){
                         return $.ajax({
                            method: "GET",
                            url: "{{url('admin/assess/get_category_list')}}",
                        }).done(function (result) {
                             // console.log(result);
                         })
                    },
                    updateItem:function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/assess/update_category')}}",
                            data: {
                                item,
                                _token : $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            console.log(result);

                        });

                    }
                },

                fields: [
                    { name: "Category_Name", type: "text", width: 100, validate: "required",css:"text-center",title:"Category Name" },
                    { name: "Happy_Zone", type: "number", width: 100, css:"text-center",title:"Happy Zone Score"},
                    { name: "Excellent", type: "number", width: 100 ,css:"text-center",title:"Excellent Score"},
                    { name: "Good", type: "number",width:100,css:"text-center",title:"Good Score"},
                    { name:"ID" ,type: "hidden", css: 'hide'},
                    { name: "Bad", type: "number",width:100,css:"text-center",title:"Bad Score"},
                    { type: "control",deleteButton: false,search:false }
                ]
            });

        });
    </script>
@endsection