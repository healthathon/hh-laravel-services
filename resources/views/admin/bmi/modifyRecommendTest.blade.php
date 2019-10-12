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
                    <h4 class="card-title mb-4">Recommend Test For Deviation Range</h4>
                </div>
                <div class="card-body">
                    <div class="alert hide">This is a primary alert—check it out!</div>
                    <div class="basic-form">
                        <form>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label" for="deviation_range">Selected
                                    Range</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly value="{{ $bmiObj['deviation_range'] }}"
                                           class="form-control"
                                           id="deviation_range"/>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Regimens</label>
                                <div class="col-sm-9">
                                    <br/>
                                    <div class="row labsTestBox" id="regimenTests">
                                        @foreach($labTests as $test)
                                            <div class="col-sm-6 col-lg-4 col-xl-4">
                                                <div class="form-check mb-5 mr-5">
                                                    <input id="{{ $test['id'] }}_checkbox"
                                                           class="form-check-input styled-checkbox"
                                                           value='{{ $test['id'] }}'
                                                           {{ in_array($test['id'],$recommendedIds) ? "checked='checked" : "" }}
                                                           type="checkbox"/>
                                                    <label for="{{ $test['id'] }}_checkbox"
                                                           title="{{ $test["test_name"]  }}"
                                                           class="form-check-label check-purple">
                                                        {{$test["test_name"] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary btn-forms" id="modifyTestBtn">
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
    <script type="text/javascript">
        let alertBoxRef = $(".alert");
        $("#modifyTestBtn").on('click', function (e) {
            e.preventDefault();
            let checkedTestId = [];
            let i = 0;
            $('input[type=checkbox]:checked').map(function (_, el) {
                checkedTestId[i] = parseInt($(el).val());
                i++;
            });
            $.ajax({
                method: "POST",
                url: "{{ route('admin.bmi.test.recommend.modify') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    bmi_id: "{{ $bmiObj['id'] }}",
                    recommended_test: checkedTestId
                },
                success: function (response) {
                    if ("error" in response) {
                        alertBoxRef.addClass("alert-danger")
                            .removeClass("hide").text(response["error"]);
                    } else {
                        alertBoxRef.addClass("alert-success")
                            .removeClass("hide")
                            .text(response['data']);
                        alertBoxRef.delay(2000).slideUp();
                    }
                },
                error: function (data) {
                }
            });
        });
    </script>
@endsection