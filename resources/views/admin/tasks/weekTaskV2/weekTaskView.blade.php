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
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit {{ $regimenName }} Week Tasks</h4>
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
                editing: false,
                deleting: true,
                inserting: false,
                sorting: false,
                paging: true,
                filtering: false,
                pageSize: 10,
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
                    insertItem: function (updateItem) {
                        let formData = getFormDataObj(updateItem);
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
                        let formData = getFormDataObj(updateItem);
                        console.error(formData);
                        {{--return $.ajax({--}}
                        {{--method: "post",--}}
                        {{--url: "{{url('/admin/task/updatePhysicsWeekTask')}}",--}}
                        {{--data: formData,--}}
                        {{--contentType: false,--}}
                        {{--processData: false,--}}
                        {{--dataType: 'json',--}}

                        {{--}).done(function (result) {--}}
                        {{--console.log(result);--}}
                        {{--});--}}
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
                        });
                    }
                },
                fields: [
                    {
                        name: "week",
                        editing: false,
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
                        title: "Tue",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day2_message",
                        type: "text",
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
                        title: "Wed",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day3_message",
                        type: "text",
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
                        title: "Thu",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day4_message",
                        type: "text",
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
                        title: "Fri",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day5_message",
                        type: "text",
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
                        title: "Sat",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day6_message",
                        type: "text",
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
                        title: "Sun",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day7_message",
                        type: "text",
                        width: 150,
                        title: "Sun Message",
                        css: "text-center",
                        align: "center"
                    },
                    {
                        name: "day7_badge",
                        type: "text",
                        width: 150,
                        title: "Sun Badge",
                        css: "text-center",
                        align: "center",
                        itemTemplate: function (item) {
                            let image = (item === null ? "" : item);
                            return "<img src='" + image + "' alt='Day Badge' width='100' height='100'/>";
                        }
                    },
                    {
                        name: "badge",
                        itemTemplate: function (val, item) {
                            return $("<img>").attr("src", val).css({height: 60, width: 60});
                        },
                        insertTemplate: function () {
                            return this.insertControl = $("<input>").prop("type", "file");
                        },
                        insertValue: function () {
                            return this.insertControl[0].files[0];
                        },
                        editTemplate: function (val, item) {
                            return this.insertControl = $("<input>").prop("type", "file");
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
                        name: "Edit", width: 200, css: "text-center", title: "View Weekly Tasks", align: "center",
                        itemTemplate: function (value, item) {
                            let url = "{{ route("admin.regimen.week_edit_page",[":regimenCode",":weekNo"]) }}";
                            url = url.replace(":regimenCode", "{{ $regimenCode }}").replace(":weekNo", item.week);
                            var $link = $("<a>").attr("href", url).text("Edit Task");
                            return $("<div>").append($link);
                        }
                    },
                ]
            });
        });

        function getFormDataObj(item) {
            let formData = new FormData();
            formData.append("ID", item.ID);
            if (item.badge != null)
                formData.append("badge", item.badge, item.badge.name);
            formData.append("week", item.week);
            formData.append("day1_title", item.day1_title);
            formData.append("day1_message", item.day1_title);
            formData.append("day2_title", item.day2_title);
            formData.append("day2_message", item.day2_title);
            formData.append("day3_title", item.day3_title);
            formData.append("day3_message", item.day3_title);
            formData.append("day4_title", item.day4_title);
            formData.append("day4_message", item.day4_title);
            formData.append("day5_title", item.day5_title);
            formData.append("day5_message", item.day5_title);
            formData.append("day6_title", item.day6_title);
            formData.append("day6_message", item.day6_title);
            formData.append("day7_title", item.day7_title);
            formData.append("day7_message", item.day7_title);
            formData.append("taskBank_id", "{{$regimenCode}}");
            return formData;
        }
    </script>
@endsection