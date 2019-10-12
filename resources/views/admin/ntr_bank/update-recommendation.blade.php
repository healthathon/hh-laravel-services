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
                    <h4 class="card-title mb-4">Recommend Regimen For Nutrition Score Range</h4>
                </div>
                <div class="card-body">
                    <div class="alert hide">This is a primary alert—check it out!</div>
                    <div class="basic-form">
                        <form>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label" for="expression">Selected
                                    Expression</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly value="{{ $data['expression'] }}"
                                           class="form-control"
                                           id="expression"/>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-3 col-form-label text-label">Regimens</label>
                                <div class="col-sm-9">
                                    <br/>
                                    <div class="row labsTestBox" id="regimenTests">
                                        @foreach($regimens as $regimen)
                                            <div class="col-sm-6 col-lg-4 col-xl-4">
                                                <div class="form-check mb-5 mr-5">
                                                    <input id="{{ $regimen['id'] }}_checkbox"
                                                           class="form-check-input styled-checkbox"
                                                           value='{{ $regimen['id'] }}'
                                                           {{ in_array($regimen['id'],$recommendedTaskIds) ? "checked='checked" : "" }}
                                                           type="checkbox"/>
                                                    <label for="{{ $regimen['id'] }}_checkbox"
                                                           title="{{ $regimen["title"]  }}"
                                                           class="form-check-label check-purple">
                                                        {{$regimen["task_name"] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary btn-forms" id="modifyRegimenBtn">
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
        $("#modifyRegimenBtn").on('click', function (e) {
            e.preventDefault();
            let checkedRegimenId = [];
            let i = 0;
            $('input[type=checkbox]:checked').map(function (_, el) {
                checkedRegimenId[i] = parseInt($(el).val());
                i++;
            });
            $.ajax({
                method: "POST",
                url: "{{ route('admin.ntr_bank.insert.recommend') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    nutrition_bank_id: "{{ $data['id'] }}",
                    recommended_task: checkedRegimenId
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