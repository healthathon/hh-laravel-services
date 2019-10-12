@extends('admin.layouts.template')
@section('stylesheet')
    <style>
        * {
            color: black;
        }

        .hide {
            display: none;
        }

        input[type=checkbox].styled-checkbox + label {
            word-break: break-word;
        }

        .labsTestBox {
            max-height: 400px;
            overflow-y: scroll;
        }
    </style>
@endsection

@section('main-content')
    <div class="row page-titles">
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-4">Edit Advise</h4>
                </div>
                <div class="card-body">
                    <div class="alert hide" id="messageBox">This is a primary alert—check it out!</div>
                    <div class="basic-form">
                        <form>
                            <div class="form-group">
                                <label class="text-label">Regimen Code</label>
                                <input type="text" readonly id="badge" class="form-control" value="{{ $code }}"
                                       placeholder="Regimen Code">
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="week">Week no</label>
                                <select class="form-control" id="week">
                                    @for($i = 1 ; $i <= $totalWeeks ; $i++)
                                        <option value="{{ $i }}" {{ $i == $week ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="text-label" for="week">Advise</label>
                                <div id="ck_editor" style="visibility: hidden; display: none;">
                                    {!!  $weekInfo["advise"] !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary btn-forms" id="updateAdviseBtn">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset("admin/assets/plugins/ckeditor/ckeditor.js") }}"></script>
    <script src="{{ asset("admin/js/plugins-init/editor-ck-init.js")}}"></script>
    <script type=" text/javascript">
        let messageBoxRef = $("#messageBox");
        $(document).ready(function () {
            $("#week").on("change", function (e) {
                e.preventDefault();
                let week = $(this).val();
                let url = "{{ route("admin.regimen.weekly.advise.info",[":week",":code"]) }}";
                url = url.replace(":week", week).replace(":code", "{{ $code }}");
                $.ajax({
                    method: "get",
                    url: url,
                    beforeSend: function () {
                        messageBoxRef
                            .removeClass("hide")
                            .toggleClass("alert-primary")
                            .text("Fetching Advise");
                    },
                    success: function (response) {
                        if ("data" in response) {
                            messageBoxRef.addClass("hide");
                            CKEDITOR.instances["ck_editor"].setData(response["data"]);
                        } else {
                            messageBoxRef.toggleClass("alert-danger").text(response["error"]);
                        }
                    },
                    error: function (e) {
                        messageBoxRef.addClass("alert-danger").text(e.toString());
                        console.log("Serious Error" + e.toString());
                    }
                })
            });

            $("#updateAdviseBtn").on("click", function (e) {
                e.preventDefault();
                let week = $("#week").val();
                let advise = CKEDITOR.instances["ck_editor"].getData();
                $.ajax({
                    method: "put",
                    url: "{{ route("admin.regimen.weekly.advise.info.update") }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        week: week,
                        code: "{{ $code }}",
                        advise: advise
                    },
                    beforeSend: function () {
                        messageBoxRef
                            .removeClass("hide")
                            .addClass("alert-primary")
                            .text("Updating Advise");
                    },
                    success: function (response) {
                        showMessageBoxMessage(response);
                    },
                    error: function (response) {
                        messageBoxRef.toggleClass("alert-danger").text(response.toString());
                    }
                });
            });

            function showMessageBoxMessage(response) {
                if ("error" in response) {
                    messageBoxRef.toggleClass("alert-danger").text(response["error"]);
                } else {
                    messageBoxRef.toggleClass("alert-success").text(response["data"]);
                }
            }
        });
    </script>
@endsection