@extends('layouts.auth')

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h4>Login</h4>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
            @csrf

            <input type="hidden" name="redirectTo" id="redirect-to" value="{{\Route::currentRouteName()}}" />

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" tabindex="1" required autocomplete="email" autofocus />
                @error('email')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="d-block">
                    <label for="password" class="control-label">Password</label>
                </div>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    tabindex="2" name="password" required autocomplete="current-password">

                @error('password')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="remember" tabindex="3" id="remember-me"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="remember-me">Remember Me</label>
                    <div class="float-right">
                        @if (Route::has('password.request'))
                        <a class="text-small" href="{{ url(\Route::current()->action['prefix'].'/password/reset') }}">
                            Forgot Password?
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <div class="ml-1">
                        @if (\Route::current()->action['prefix'] == '/business')
                            <a href="{{ route('login') }}">
                                <strong>Admin Login</strong>
                            </a>
                        @endif
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block">
                            Login
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block">
                            Sign Up
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
