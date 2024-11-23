<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="96x96" href="{!! asset('favicon/favicon-96x96.png') !!}">
    <link rel="icon" type="image/png" sizes="96x96" href="{!! asset('favicon/favicon-96x96.png') !!}">
    <link rel="shortcut icon" href="{!! asset('favicon/favicon-32x32.png') !!}">
    <link rel="manifest" href="{!! asset('favicon/manifest.json') !!}">
    <meta name="msapplication-TileColor" content="#43425D">
    <meta name="msapplication-TileImage" content="{!! asset('favicon/ms-icon-144x144.png') !!}">
    <meta name="theme-color" content="#43425D">

    <meta property="og:title" content="support" />
    <meta property="og:site_name" content="support" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="" />
    <meta property="og:type" content="website" />
    <title>Support</title>
    <link rel="stylesheet" href="{!! asset('plugins/bootstrap/css/bootstrap.min.css') !!}">

    <!-- General CSS Files -->
    <style>
        .page{
            z-index: 1;
            max-width: 450px;
            min-height: 450px;
            background-image: none;
            border-width: 0px;
            border-color: #000000;
            background-color: transparent;
            padding-left: 25px;
            padding-top: 17px;
            padding-right: 25px;
            margin-left: auto;
            margin-right: auto;
        }
        img {
            width: 100%;
        }

        figure{
            margin: 0 !important;
        }

        /*  h1.title {
             text-align: center;
         } */

        @media (max-width: 767px){
            .page{
                width: calc(100% - 55px);
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
    <link rel="stylesheet" href="{!! asset('plugins/editor/style.css') !!}">
    @yield('styles')
</head>

<body>
<div id="app" class="page">
    <h3 class="title mb-3">Support</h3>
    <div class="row mb-1">
        <div class="col-6">E-mail</div>
        <div class="col-6">serenely0@gmail.com</div>
    </div>
    <div class="row mb-1">
        <div class="col-6">Contact Number</div>
        <div class="col-6">01087950930</div>
    </div>
    <div class="row">
        <div class="col-6">Address</div>
        <div class="col-6">서울 강남구 영동대로 517 <br> 아셈타워 30층 루미너스 인베스트먼트</div>
    </div>
</div>

{{--<script src="{!! asset('plugins/bootstrap/js/bootstrap.min.js') !!}"></script>--}}
</body>
</html>
