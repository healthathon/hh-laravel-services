<!DOCTYPE html>
<html lang="en" class="h-100" id="login-page1">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Health Admin Dashboard Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('admin/assets/images/favicon.png')}}">
    <!-- Custom Stylesheet -->
    <link href="{{asset('admin/css/style.css')}}" rel="stylesheet">

    <style>
        #login-page1 body{
            background: black;
        }
        .invalid-feedback {
            display: block !important;
        }
    </style>

</head>

<body class="h-100">
<div id="preloader">
    <div class="loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"/>
        </svg>
    </div>
</div>
<div class="login-bg h-100">
    <div class="container h-100">
        <div class="row justify-content-center h-100">
            <div class="col-xl-5">
                <div class="form-input-content login-form">
                    <div class="card">
                        <div class="card-body">
                            <div class="logo text-center">
                                {{--<a href="index.html">--}}
                                <img src="{{asset('images/hhlogo.png')}}" alt="" height="80" width="80">
                                {{--</a>--}}
                            </div>
                            <h4 class="text-center mt-4">Log into Your Account</h4>
                            <form class="mt-5 mb-5" action="{{route('admin.login.submit')}}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" placeholder="Email" name="email">
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" placeholder="Password" name="password">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                    </div>
                                    <div class="form-group col-md-6 text-right"><a href="javascript:void(0)">Forgot
                                            Password?</a>
                                    </div>
                                </div>
                                <div class="text-center mb-4 mt-4">
                                    <button type="submit" class="btn btn-primary">Sign in</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #/ container -->
<!-- Common JS -->
<script src="{{asset('admin/assets/plugins/common/common.min.js')}}"></script>
<!-- Custom script -->
<script src="{{asset('admin/js/custom.min.js')}}"></script>
<script src="{{asset('admin/js/settings.js')}}"></script>
<script src="{{asset('admin/js/gleek.js')}}"></script>
</body>

</html>