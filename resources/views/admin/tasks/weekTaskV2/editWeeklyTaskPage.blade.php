@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/ratchet/2.0.2/css/ratchet.css" rel="stylesheet"/>
    <style type="text/css">
        .error, .messageBox {
            display: none;
        }

        * {
            color: black;
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

        label.hide {
            display: none;
        }
    </style>
@endsection

@section('main-content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card forms-card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        Edit Week days Task
                    </h4>
                    <div class="alert alert-danger">
                        <small>NOTE: If Message is Empty then system will store default message as
                            <b> <i>Congratulations</i> </b>
                        </small>
                    </div>
                    <div class="alert messageBox">
                    </div>
                    <div class="basic-form">
                        <div class="form-group row align-items-center">
                            <label class="col-sm-2 col-form-label text-label">Select Week</label>
                            <div class="col-sm-10">
                                <select id="week" name="week" class="form-control">
                                    @foreach($weeksCountArr as $week)
                                        <option value="{{ $week }}" {{ $week == $weekNo ? "selected='selected'" : "" }}>{{ $week }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="jsGrid-basic"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{asset('admin/assets/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('admin/assets/plugins/jsgrid/js/jsgrid.min.js')}}"></script>
    <script type="text/javascript">
        $("#week").on('change', function (e) {
            $("#jsGrid-basic").jsGrid('loadData');
        });
        $("document").ready(function ($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            'use strict';
            $("#jsGrid-basic").jsGrid({
                width: "100%",
                height: "600px",
                editing: true,
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
                        let weekNo = $("#week").val();
                        let url = "{{ route("admin.regimen.week.info.get",[":regimenCode",":weekNo"]) }}";
                        url = url.replace(":regimenCode", "{{ $regimenCode }}");
                        url = url.replace(":weekNo", weekNo.toString());
                        return $.ajax({
                            method: "GET",
                            url: url
                        });
                    },
                    updateItem: function (item) {
                        let weekNo = $("#week").val();
                        let url = "{{ route('admin.regimen.week.info.update',['taskBankId' => ':taskBankId','weekNo' =>':weekNo']) }}";
                        url = url.replace(":taskBankId", "{{ $regimenCode }}").replace(":weekNo", weekNo.toString());
                        $.ajax({
                            method: "patch",
                            url: url,
                            data: {
                                item,
                                __token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                showSuccessOrFailureMessageAlert(response);
                            },
                            error: function (error) {
                                console.log(error);
                            }
                        });
                    },
                    deleteItem: function (item) {
                        alert("Service under development");
                    }
                },
                fields: [
                    {
                        name: "week", type: "number", width: 50, validate: "required",
                        title: "Week", css: "text-center", align: "center", editing: false
                    },
                    {
                        name: "day", type: "number", width: 50, validate: "required", title: "Day", css: "text-center",
                        align: "center", editing: false
                    },
                    {
                        name: "title", type: "text", width: 150, validate: "required",
                        title: "Title", css: "text-center", align: "center"
                    },
                    {
                        name: "message", type: "text", width: 150,
                        title: "Message", css: "text-center", align: "center"
                    },
                    {
                        name: "badge", type: "read-only", title: "Badge", css: "text-center", editing: false,
                        itemTemplate: function (item, value) {
                            let day = value.day;
                            // const id = value.ID;
                            const badgeUrl = value.badge;
                            let eleId = "file_" + day;
                            let onChangeElement = "onchange=javascript:uploadDailyBadge('" + value.week + "',this,\'" + day + "\');";
                            let $fileLink = "<br/><br/><form enctype='multipart/form-data'>";
                            $fileLink += "<label for='" + eleId + "' class='regimen_text'>Upload Badge</label>";
                            $fileLink += "<input id='" + eleId + "'" + onChangeElement + " type='file'  style='visibility: hidden;'/>";
                            $fileLink += "</form>";
                            let $imageLink = "<img src='" + (badgeUrl == null ? "" : badgeUrl) + "' alt='regimen logo' height='100' width='100'/>";
                            return $("<div>").append($imageLink).append($fileLink)
                        },
                        itemValue: function (item, value) {
                            return $("#file_" + value.day).files;
                        }
                    },
                    {
                        type: "control", editButton: true, deleteButton: false
                    },
                ]
            });
        });

        function uploadDailyBadge(week, e, day) {
            let file = e.files[0];
            let messageBoxRef = $(".messageBox");
            let formData = new FormData();
            formData.append("regimenCode", "{{$regimenCode}}");
            formData.append("week", week);
            formData.append("day", day);
            formData.append("fileData", file);
            formData.append("_token", "{{ csrf_token() }}");
            messageBoxRef.css("display", "block");
            messageBoxRef.addClass("alert-primary");
            $.ajax({
                method: "POST",
                url: "{{route("admin.regimen.daily.badge.upload")}}",
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

        function showSuccessOrFailureMessageAlert(response) {
            const labelMessageRef = $(".messageBox");
            $(labelMessageRef).css("display", "block");
            if ("error" in response)
                $(labelMessageRef).addClass("alert alert-danger").text(response["error"]);
            else {
                $(labelMessageRef).addClass("alert alert-success").text(response["data"]);
            }
            $("#jsGrid-basic").jsGrid("loadData")
        }
    </script>
@endsection
