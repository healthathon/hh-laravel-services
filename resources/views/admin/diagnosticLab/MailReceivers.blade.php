@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <style>
        * {
            color: black;
        }

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
                    <h4 class="card-title mb-4">MMG Mail Receivers</h4>
                    <div class="alert hide" id="messageBox"></div>
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
                height: "600px",

                editing: true,
                deleting: true,
                inserting: true,
                sorting: true,
                paging: true,
                filtering: true,
                pageSize: 10,
                pageButtonCount: 5,
                autoload: true,
                validate: true,
                controller: {
                    loadData: function (filter) {
                        return $.ajax({
                            method: "GET",
                            dataType: "json",
                            url: "{{route("admin.mmg.mail.receivers.info.get")}}",
                        }).done(function (result) {
                        })
                    },
                    insertItem: function (item) {
                        return $.ajax({
                            method: "post",
                            data: {
                                item,
                                "_token": "{{ csrf_token() }}"
                            },
                            url: "{{route("admin.mmg.mail.receivers.info.save")}}",
                        }).done(function (result) {
                            showAlert(result);
                        })
                    },
                    deleteItem: function (item) {
                        let url = "{{route("admin.mmg.mail.receivers.info.delete",":id")}}";
                        url = url.replace(":id", item.id);
                        return $.ajax({
                            method: "delete",
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            url: url
                        }).done(function (result) {
                            showAlert(result);
                        })
                    },
                    updateItem: function (item) {
                        let url = "{{route("admin.mmg.mail.receivers.info.update",":id")}}";
                        url = url.replace(":id", item.id);
                        return $.ajax({
                            method: "put",
                            data: {
                                item,
                                "_token": "{{ csrf_token() }}"
                            },
                            url: url
                        }).done(function (result) {
                            showAlert(result);
                        })
                    },
                },
                fields: [
                    {name: "id", type: "number", css: 'hide'},
                    {name: "user_name", type: "text", width: 100, css: "text-center", title: "User Name"},
                    {
                        name: "user_email",
                        type: "text",
                        width: 100,
                        css: "text-center",
                        title: "User Email"
                    },
                    {
                        name: "to_send", type: "text", width: 100, css: "text-center", title: "Is Active",
                        itemTemplate: function (item, value) {
                            let eleRef = "<select>";
                            eleRef += "<option value='1'" + (value.to_send === 1 ? 'selected' : '') + ">Activated</option>";
                            eleRef += "<option value='0'" + (value.to_send === 0 ? 'selected' : '') + ">De-Activated</option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        editTemplate: function (item) {
                            let eleRef = "<select id='updateToSendValue'>";
                            eleRef += "<option value='1'" + (item === 1 ? 'selected' : '') + ">Activated</option>";
                            eleRef += "<option value='0'" + (item === 0 ? 'selected' : '') + ">De-Activated</option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        editValue: function () {
                            return $("#updateToSendValue").val();
                        },
                        insertTemplate: function () {
                            let eleRef = "<select id='insertToSendValue'>";
                            eleRef += "<option value='1'>Activated</option>";
                            eleRef += "<option value='0'>De-Activated</option>";
                            eleRef += "</select>";
                            return eleRef;
                        },
                        insertValue: function () {
                            return $("#insertToSendValue").val();
                        }
                    },
                    {type: "control", deleteButton: true, editButton: true, title: "Actions"}
                ],
                noDataContent: "No MapMyGenome Mail Receiver Member Found"
            });
        });

        function showAlert(response) {
            let messageBoxRef = $("#messageBox");
            messageBoxRef.removeClass("hide");
            if ("error" in response) {
                messageBoxRef.removeClass("alert-success").addClass("alert alert-danger");
                messageBoxRef.text(response["error"]);
            } else {
                messageBoxRef.removeClass("alert-danger").addClass("alert alert-success");
                messageBoxRef.text(response["data"]);
            }
            $("#jsGrid-basic").jsGrid("loadData");
        }
    </script>
@endsection