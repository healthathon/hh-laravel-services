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
    <script>
        var tags =<?php echo $result?>;
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
        $(function ($) {
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
                            url: "{{url('admin/assess/get_question_list')}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    insertItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/assess/insert_question')}}",
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
                            url: "{{url('admin/assess/update_question')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            reloadData();
                            // console.log(result);
                        });
                    },
                    deleteItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{url('admin/assess/delete_question')}}",
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
                    {name: "Query", type: "text", width: 150, validate: "required", title: "Question Text"},
                    {
                        name: "Tag", type: "select", width: 150, validate: "required", css: "text-center", title: "Tag",
                        items: [
                            {Name: null, Id: 0},
                            {Name: tags[0]['tag_name'], Id: tags[0]['tag_id']},
                            {Name: tags[1]['tag_name'], Id: tags[1]['tag_id']},
                            {Name: tags[2]['tag_name'], Id: tags[2]['tag_id']},
                            {Name: tags[3]['tag_name'], Id: tags[3]['tag_id']},
                            {Name: tags[4]['tag_name'], Id: tags[4]['tag_id']},
                            {Name: tags[5]['tag_name'], Id: tags[5]['tag_id']},
                            {Name: tags[6]['tag_name'], Id: tags[6]['tag_id']},
                        ],
                        valueField: "Id",
                        textField: "Name",
                        filtering: true,
                    },
                    {name: "ID", type: "hidden", css: 'hide'},
                    {
                        name: "Result_String", type: "text", width: 150, css: "text-center", title: "Answer String"
                    },
                    {name: "Result_Mark", type: "text", width: 150, css: "text-center", title: "Answer Marks"},
                    {
                        name: "restricted_level",
                        type: "text",
                        width: 150,
                        css: "text-center",
                        title: "Level Restriction CRT  Answers"
                    },
                    {
                        name: "is_mental_bank", type: "select", width: 150, css: "text-center", title: "Is Mental Bank",
                        itemTemplate: function (item) {
                            let eleRef = "<select disabled='disabled' class='form-control'>";
                            eleRef += "<option value='1'" + (item === true ? "selected" : "") + ">True</option>";
                            eleRef += "<option value='0'" + (item === false ? "selected" : "") + ">False</option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        editTemplate: function (item) {
                            let eleRef = "<select class='form-control' id='isMentalBnkValue'>";
                            eleRef += "<option value='1'" + (item === true ? "selected" : "") + ">True</option>";
                            eleRef += "<option value='0'" + (item === false ? "selected" : "") + ">False</option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        editValue: function () {
                            return $("#isMentalBnkValue").val();
                        },
                        insertTemplate: function () {
                            let eleRef = "<select class='form-control' id='isMentalBnkValueInsert'>";
                            eleRef += "<option value='1'> True </option>";
                            eleRef += "<option value='0'> False </option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        insertValue: function () {
                            return $("#isMentalBnkValueInsert").val();
                        }
                    },
                    {type: "control"}
                ]
            });
            $("#jsGrid-basic").jsGrid("option", "filtering", false);
        });

        function reloadData() {
            $("#jsGrid-basic").jsGrid("loadData");
        }
    </script>
@endsection