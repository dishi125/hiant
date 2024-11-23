<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="96x96" href="{!! asset('favicon/favicon-96x96.png') !!}">
    <?php /*<link rel="apple-touch-icon" sizes="60x60" href="{!! asset('favicon/apple-icon-60x60.png') !!}">
    <link rel="apple-touch-icon" sizes="72x72" href="{!! asset('favicon/apple-icon-72x72.png') !!}">
    <link rel="apple-touch-icon" sizes="76x76" href="{!! asset('favicon/apple-icon-76x76.png') !!}">
    <link rel="apple-touch-icon" sizes="114x114" href="{!! asset('favicon/apple-icon-114x114.png') !!}">
    <link rel="apple-touch-icon" sizes="120x120" href="{!! asset('favicon/apple-icon-120x120.png') !!}">
    <link rel="apple-touch-icon" sizes="144x144" href="{!! asset('favicon/apple-icon-144x144.png') !!}">
    <link rel="apple-touch-icon" sizes="152x152" href="{!! asset('favicon/apple-icon-152x152.png') !!}">
    <link rel="apple-touch-icon" sizes="180x180" href="{!! asset('favicon/apple-icon-180x180.png') !!}">
    <link rel="icon" type="image/png" sizes="192x192" href="{!! asset('favicon/android-icon-192x192.png') !!}">
    <link rel="icon" type="image/png" sizes="32x32" href="{!! asset('favicon/favicon-32x32.png') !!}">
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('favicon/favicon-16x16.png') !!}"> */ ?>
    <link rel="icon" type="image/png" sizes="96x96" href="{!! asset('favicon/favicon-96x96.png') !!}">
    <link rel="shortcut icon" href="{!! asset('img/logo.png') !!}">
    <link rel="manifest" href="{!! asset('favicon/manifest.json') !!}">
    <meta name="msapplication-TileColor" content="#43425D">
    <meta name="msapplication-TileImage" content="{!! asset('favicon/ms-icon-144x144.png') !!}">
    <meta name="theme-color" content="#43425D">

    <title>@if (@$title) {{ @$title }} - @endif {{ config('app.name', 'Laravel') }}</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{!! asset('plugins/bootstrap/css/bootstrap.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('plugins/fontawesome/css/all.min.css') !!}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('plugins/izitoast/css/iziToast.css') }}">
    <style type="text/css">
        .copy_clipboard,.copy_code {
            cursor: pointer;
        }
    </style>
    @yield('styles')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{!! asset('css/style.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/responsive.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/components.css') !!}">
    <link rel="stylesheet" href="{!! asset('plugins/select2/dist/css/select2.min.css') !!}">

</head>

<body onload="initializeApp()">
    <?php
        $user = \Auth::user();
//        $settings = App\Models\GeneralSettings::whereIn('key',[App\Models\GeneralSettings::IOS_APP_VERSION])->first();
//        $displaysettings = App\Models\GeneralSettings::whereIn('key',[App\Models\GeneralSettings::DISPLAY_APP_VERSION])->first();
    ?>
    <div id="app">
        <div class="main-wrapper">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <div class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                                <i class="fas fa-bars"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="pt-1 text-white">
                    {{ __('menu.supporter_code') }} : <b style="text-decoration: underline;">{{Auth::user()->recommended_code}}</b>
                </div>
                <ul class="navbar-nav navbar-right user-navbar-nav">

                    <li class="dropdown"><a href="#" data-toggle="dropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <?php
                                $avatar = Auth::user()->avatar;
                            ?>
                            <img alt="image" src="@if (@$avatar) {!! asset($avatar) !!} @else {!! asset('img/avatar/avatar-1.png') !!} @endif" class="rounded-circle mr-1">

                            <div class="d-sm-none d-lg-inline-block">Hi {{ Auth::user()->name }}</div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-title">Welcome, {{ Auth::user()->name }}</div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item has-icon text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                <input type="hidden" name="redirectTo" id="redirect-to" value="{{\Route::currentRouteName()}}" />
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>

                <?php
                    $user = \Auth::user();
                    $checked = ($user && $user->lang_id == 1) ? 'checked' : '';
                ?>
                <label class="toggleSwitch nolabel language-toggle-switch" onclick="">
                    <input type="checkbox"  id="update_language" class="update_language" userid="{{ $user->id }}" {{ $checked }} />
                    <span>
                        <span>English</span>
                        <span>Korean</span>
                    </span>
                    <a></a>
                </label>
            </nav>
            <div class="main-sidebar">
                @include('layouts.sidebar')
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        @yield('header-content')
                    </div>

                    <div class="section-body">
                        @yield('content')
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <div class="footer-left">
                    Copyright &copy; {{date('Y')}}
                    <div class="bullet"></div> {{ config('app.name') }}
                </div>
                <div class="footer-right">
                    1.5.0
                </div>
            </footer>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{!! asset('plugins/jquery.min.js') !!}"></script>
    <script src="{!! asset('plugins/popper.js') !!}"></script>
    <script src="{!! asset('plugins/tooltip.js') !!}"></script>
    <script src="{!! asset('plugins/bootstrap/js/bootstrap.min.js') !!}"></script>
    <script src="{!! asset('plugins/nicescroll/jquery.nicescroll.min.js') !!}"></script>
    <script src="{!! asset('plugins/moment.min.js') !!}"></script>
    <script src="{!! asset('plugins/jquery-validation/jquery.validate.js') !!}"></script>
    <script src="{!! asset('plugins/jquery-validation/additional-methods.js') !!}"></script>
    <script src="{!! asset('js/stisla.js') !!}"></script>
    <script src="{!! asset('plugins/select2/dist/js/select2.full.min.js') !!}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset('plugins/izitoast/js/iziToast.js') }}"></script>
    @yield('scripts')

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/bootstrap-modal.js')}}"></script>

    <!-- Template JS File -->
    <script src="{!! asset('js/scripts.js') !!}"></script>

    <!-- Custom scripts for all pages -->
    <script>
        var baseUrl = "{{ url('/') }}";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.dropdown-toggle').dropdown();
    </script>

    <script src="{!! asset('js/custom.js') !!}"></script>
    @yield('page-script')

    @include('vendor.lara-izitoast.toast')
</body>

</html>
