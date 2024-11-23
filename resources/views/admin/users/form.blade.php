@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="{!! asset('css/custom.css') !!}">
@endsection
@section('header-content')
    <h1>@if (@$title) {{ @$title }} @endif</h1>
@endsection

@section('content')
    <div class="section-body">
        <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card profile-widget">
                    <div class="profile-widget-description">
                        <div class="">
                            <form id="UserForm" method="post" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body" id="card_body_div">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('username', __(Lang::get('forms.user.username'))) !!}
                                                {!! Form::text('username', '', [
                                                       'class' => 'form-control' . ($errors->has('username') ? ' is-invalid' : ''),
                                                       'placeholder' => __(Lang::get('forms.user.username')),
                                               ]) !!}
                                                @error('username')
                                                <div class="invalid-feedback">
                                                    @foreach($errors->get('username') as $error)
                                                        {{ $error }}
                                                    @endforeach
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('email', __(Lang::get('forms.user.email'))) !!}
                                                {!! Form::text('email', '', [
                                                       'class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''),
                                                       'placeholder' => __(Lang::get('forms.user.email')),
                                               ]) !!}
                                                @error('email')
                                                <div class="invalid-feedback">
                                                    @foreach($errors->get('email') as $error)
                                                        {{ $error }}
                                                    @endforeach
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('phone_number', __(Lang::get('forms.user.phone_number'))) !!}
                                                {!! Form::text('phone_number', '', [
                                                       'class' => 'form-control' . ($errors->has('phone_number') ? ' is-invalid' : ''),
                                                       'placeholder' => __(Lang::get('forms.user.phone_number')),
                                               ]) !!}
                                                @error('phone_number')
                                                <div class="invalid-feedback">
                                                    @foreach($errors->get('phone_number') as $error)
                                                        {{ $error }}
                                                    @endforeach
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('password', __(Lang::get('forms.user.password'))) !!}
                                                <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="{{ __(Lang::get('forms.user.password')) }}">
                                                @error('password')
                                                <div class="invalid-feedback">
                                                    @foreach($errors->get('password') as $error)
                                                        {{ $error }}
                                                    @endforeach
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('password_confirmation', __(Lang::get('forms.user.password_confirmation'))) !!}
                                                <input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" placeholder="{{ __(Lang::get('forms.user.password_confirmation')) }}">
                                                @error('password_confirmation')
                                                <div class="invalid-feedback">
                                                    @foreach($errors->get('password_confirmation') as $error)
                                                        {{ $error }}
                                                    @endforeach
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select name="gender" class="form-control">
                                                    <option selected disabled>Select gender</option>
                                                    <option value="1">Man</option>
                                                    <option value="2">Women</option>
                                                </select>
                                                @error('gender')
                                                <div class="invalid-feedback">
                                                    @foreach($errors->get('gender') as $error)
                                                        {{ $error }}
                                                    @endforeach
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary" id="btn_submit">{{ __(Lang::get('general.save')) }}</button>
                                    <a href="{{ route('admin.user.index')}}" class="btn btn-default">{{ __(Lang::get('general.cancel')) }}</a>
                                </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cover-spin"></div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('#UserForm').validate({
            rules: {
                'username': {
                    required: true
                },
                'email': {
                    required: true,
                    email: true
                },
                'phone_number': {
                    required: true
                },
                'password': {
                    required: true
                },
                'password_confirmation': {
                    required: true
                },
                'gender': {
                    required: true
                },
            },
            highlight: function(input) {
                $(input).parents('.form-line').addClass('error');
            },
            unhighlight: function(input) {
                $(input).parents('.form-line').removeClass('error');
            },
            errorPlacement: function(error, element) {
                $(element).parents('.form-group').append(error);
            },
        });
    </script>
@endsection
