<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Health App Dashboard</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon.png')}}">
    @yield('stylesheet')
    <link href="{{asset('admin/css/style.css')}}" rel="stylesheet">
    <style>
        .uploadMessage,.hide {
            display: none;
        }

        * {
            font-family: "Courier New", Courier, monospace;
        }
    </style>

</head>