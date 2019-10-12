@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        * {
            color: black;
        }

        .hide, .messageBox {
            display: none;
        }

        label.regimen_text {
            border: 1px solid black;
            color: #000;
            cursor: pointer;
            background-color: whitesmoke;
            padding: 10px;
            -webkit-box-shadow: 2px 0 1px #000;
            -moz-box-shadow: 1px 0 1px #000;
            box-shadow: 2px 0 1px #000;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
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
                    <h4 class="card-title mb-4">{{ $category }} Regimens</h4>
                    <div class="alert hide messageBox"></div>
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
            'use strict';
            $("#jsGrid-basic").jsGrid({
                width: "100%",
                height: "900px",
                editing: true,
                deleting: true,
                inserting: true,
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
                            url: "{{route('admin.regimen.info',['category' => $category])}}",
                        }).done(function (result) {
                        })
                    },
                    insertItem: function (item) {
                        return $.ajax({
                            method: "post",
                            url: "{{route('admin.regimen.insert')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (response) {
                            showResponseBox(response);
                        });
                    },
                    updateItem: function (item) {
                        return $.ajax({
                            method: "PUT",
                            url: "{{route('admin.regimen.update')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showResponseBox(result);
                        });
                    },
                    deleteItem: function (item) {
                        let url = '{{route("admin.regimen.delete",":regimenCode")}}';
                        url = url.replace(":regimenCode", item.code);
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
                    {name: "level", type: "text", width: 50, css: "text-center", title: "Level", align: "center"},
                    {
                        name: "task_name",
                        type: "text",
                        width: 100,
                        validate: "required",
                        title: "Regimen Name",
                        align: "center",
                    },
                    {name: "ID", type: "hidden", css: 'hide', editing: false, inserting: false},
                    {
                        name: "category", type: "hidden", css: 'hide',
                        editing: false,
                        inserting: true,
                        insertValue: function () {
                            return "{{ $categoryId }}";
                        }
                    },
                    {
                        name: "title",
                        type: "text",
                        width: 100,
                        css: "text-center",
                        title: "Title",
                        align: "center",
                        validate: "required"
                    },
                    {name: "detail", type: "text", width: 100, css: "text-center", title: "Detail", align: "center"},
                    {
                        name: "code",
                        type: "text",
                        width: 100,
                        css: "text-center",
                        title: "Regimen Code",
                        align: "center",
                        editing: false
                    },
                    {
                        name: "image",
                        // type: "file",
                        type: "text",
                        width: 150,
                        css: "text-center",
                        title: "Regimen Badge",
                        align: "center",
                        itemTemplate: function (value, item) {
                            const id = item.ID;
                            const badgeUrl = item.image;
                            let eleId = "file_" + id;
                            // let onChangeElement = "onchange=javascript:uploadRegimenBadge(this,\'" + id + "\');";
                            // let $fileLink = "<br/><br/><form enctype='multipart/form-data'>";
                            // $fileLink += "<label for='" + eleId + "' class='regimen_text'>Upload Badge</label>";
                            // $fileLink += "<input id='" + eleId + "' type='file'" + onChangeElement + " style='visibility: hidden;'/>";
                            // $fileLink += "</form>";
                            let $imageLink = "<img src='" + (badgeUrl == null ? "" : badgeUrl) + "' alt='regimen logo' height='100' width='100'/><br/>";
                            return $("<div>").append($imageLink);
                        },
                        itemValue: function (value, item) {
                            return item.image;
                        },
                    },
                    {
                        name: "week_task", width: 200, css: "text-center", title: "View Weekly Tasks", align: "center",
                        itemTemplate: function (value, item) {
                            let url = "{{ route("admin.regimen.week_details_page",":regimenCode") }}";
                            url = url.replace(":regimenCode", item.code);
                            let $text = $("<p>").text(item.MyField);
                            let $link = $("<a>").attr("href", url).text("View Weekly Tasks");
                            return $("<div>").append($text).append($link);
                        }
                    },
                    {
                        type: "control", deleteButton: true, editButton: true
                    }
                ]
            });
        });

        function showResponseBox(response) {
            let messageBoxRef = $(".messageBox");
            messageBoxRef.css("display", "block");
            if ("error" in response) {
                messageBoxRef.addClass("alert alert-danger");
                messageBoxRef.text(response["error"]);
            } else {
                messageBoxRef.addClass("alert alert-success");
                messageBoxRef.text(response["data"]);
            }
            $("#jsGrid-basic").jsGrid("loadData");
        }

        // Deprecated
        function uploadRegimenBadge(e, regimenId) {
            let file = e.files[0];
            let messageBoxRef = $("#messageBox");
            let formData = new FormData();
            formData.append("regimenId", regimenId);
            formData.append("fileData", file);
            formData.append("_token", "{{ csrf_token() }}");
            messageBoxRef.css("display", "block");
            messageBoxRef.addClass("alert-primary");
            $.ajax({
                method: "POST",
                url: "{{route("admin.regimen.badge.upload")}}",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function () {
                    messageBoxRef.text("Uploading Image.....");
                },
                success: function (response) {
                    if ("error" in response)
                        messageBoxRef.text(response["error"]);
                    else
                        messageBoxRef.text(response["data"]);
                    messageBoxRef.delay(2000).slideUp();
                    $("#jsGrid-basic").jsGrid("loadData");
                },
                error: function (response) {
                    messageBoxRef.text(response);
                }
            });
        }
    </script>
@endsection