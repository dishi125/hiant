@extends('layouts.auth')

@section('content')
<div class="card card-primary">
  <div class="card-header">
    <h4>Reset Password</h4>
  </div>
  {{ session()->put('login-route',\Route::currentRouteName())}}
  <div class="card-body">
    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <input type="hidden" name="redirectTo" id="redirect-to" value="{{\Route::currentRouteName()}}" />
      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
          tabindex="1" value="{{ old('email') }}" autofocus>
        <div class="invalid-feedback">
          {{ $errors->first('email') }}
        </div>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          Send Reset Link
        </button>
      </div>
    </form>
  </div>
</div>
<div class="mt-5 text-muted text-center">
  Recalled your login info? <a href="{{ url(\Route::current()->action['prefix'].'/login') }}">Sign In</a>
</div>
@endsection