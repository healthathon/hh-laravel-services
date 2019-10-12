@extends('admin.layouts.template')
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/jsgrid/css/jsgrid-theme.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/select2/css/select2.min.css')}}"/>
    <style>
        .hide {
            display: none;
        }

        #jsGrid-basic {
            display: block;
        }

        #newTagForm {
            display: none;
        }
    </style>
@endsection

@section('main-content')
    <div class="row page-titles">
        <div class="col p-md-0">
            <h4>Assessment Tag Order</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Assessment</a>
                </li>
                <li class="breadcrumb-item active">Ordering Tag</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Rearrange Assessment Questions Tag Order
                        &nbsp;
                        <button type="button" id="addNewTagOrder" class="btn btn-primary">Add New Tag Order</button>
                        <button type="button" id="viewAddedTags" class="btn btn-warning">Show Tag Order</button>
                    </h4>
                    <div class="alert alert-success messageBox hide">

                    </div>
                    <div id="jsGrid-basic"></div>
                    <div id="newTagForm">
                        <form class="">
                            <div class="form-group">
                                <label for="tags">Select tags</label>
                                <select class='multi-select' id='tags' name='tagIds[]' multiple='multiple'>
                                    @foreach($tags as $tag)
                                        <option value='{{ $tag['id'] }}'>{{ ucfirst($tag['tag_name']) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Activate/Deactivate</label>
                                <select class="active-status form-control" id="status">
                                    <option value="1"> Activate</option>
                                    <option value="0"> De-activate</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-default" id="saveNewTagOrderBtn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('admin/assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('admin/assets/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('admin/assets/plugins/jsgrid/js/jsgrid.min.js')}}"></script>
    <script>
    </script>
    <script>
        let addNewTagOrderRef = $("#addNewTagOrder");
        let viewAddedTagsRef = $("#viewAddedTags");
        let jsGridRef = $("#jsGrid-basic");
        let newTagFormRef = $("#newTagForm");
        $(addNewTagOrderRef).on('click', function (e) {
            e.preventDefault();
            initSelect2();
            $(this).css("border", "2px solid black");
            $(viewAddedTagsRef).css("border", "none");
            $(jsGridRef).css("display", "none");
            $(newTagFormRef).css("display", "block");
        });
        $(viewAddedTagsRef).on('click', function (e) {
            e.preventDefault();
            $(this).css("border", "2px solid black");
            $(addNewTagOrderRef).css("border", "none");
            $(jsGridRef).css("display", "block");
            $(newTagFormRef).css("display", "none");
            $('#jsGrid-basic').jsGrid("loadData");
        });

        //Save new tag ordering
        $("#saveNewTagOrderBtn").on('click', function (event) {
            event.preventDefault();
            let newOrderIds = $("#tags").val().toString();
            let status = $("#status").val();
            let tagIds = newOrderIds.split(",");
            if (tagIds.length === 0 || parseInt({{ count($tags) }}) !== tagIds.length) {
                alert("Please select all tags");
                return false;
            } else {
                $.ajax({
                    method: "POST",
                    url: "{{ route("admin.assess.postTagOrder") }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tagIds: tagIds,
                        status: status
                    },
                    success: function (data) {
                        showMessage(data);
                    },
                    error: function (error) {

                    }
                })
            }
        });
        $(function ($) {
            'use strict';
            $("#jsGrid-basic").jsGrid({
                width: "100%",
                height: "800px",

                editing: true,
                deleting: false,
                inserting: false,
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
                            url: "{{url('admin/assess/tag/sequence')}}",
                        }).done(function (result) {
                            // console.log(result);
                        })
                    },
                    updateItem: function (item) {
                        return $.ajax({
                            method: "put",
                            url: "{{route('admin.assess.update.tag.order')}}",
                            data: {
                                item,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                        }).done(function (result) {
                            showMessage(result);
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
                            showMessage(result);
                        });
                    }
                },
                fields: [
                    {
                        name: "sequence.tags",
                        type: "text",
                        width: 200,
                        validate: "required",
                        css: "text-center",
                        readonly: true,
                        title: "Sequence Order",
                        editing: false,
                    },
                    {name: "id", type: "hidden", css: 'hide'},
                    {
                        name: "is_active", type: "text",
                        width: 60, css: "text-center",
                        title: "Active Status",
                        editTemplate: function (item) {
                            console.log("ITEM  : " + item);
                            return '<select class="active-status">' +
                                '<option value="1"' + (item === 1 ? "selected='selected'" : "") + '> Activate </option>' +
                                '<option value="0" ' + (item === 0 ? "selected='selected'" : "") + '> De-activate </option>' +
                                '</select>';
                        },
                        onItemEditing: function (item, value) {
                            console.log(value);
                        },
                        editValue: function () {
                            return $("select.active-status").val();
                        }
                    },
                    {type: "control", deleteButton: false, editButton: true}
                ]
            });
        });


        // Alternate solution for work around
        function initSelect2() {
            const multiSelectRef = $(".multi-select");
            multiSelectRef.select2({
                height: "20px",
                width: "100%"
            });
            // Disable Sorting
            multiSelectRef.on("select2:select", function (evt) {
                const element = evt.params.data.element;
                const $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });
        }

        function showMessage(response) {
            let messageBoxRef = $(".messageBox");
            messageBoxRef.removeClass("hide");
            if ("error" in response) {
                messageBoxRef.addClass("alert alert-danger").text(response["error"]);
            } else {
                messageBoxRef.addClass("alert alert-success").text(response["data"]);
            }
            $("#jsGrid-basic").jsGrid("loadData");
        }
    </script>
@endsection