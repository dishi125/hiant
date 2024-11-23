@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="{!! asset('css/custom.css') !!}">
@endsection
@section('header-content')
    <h1>
        @if (@$title)
            {{ @$title }}
        @endif
    </h1>
@endsection

@section('content')
    <?php
    $data = [];
    if (!empty($settings)) {
        $data = $settings;
    }

    $id = !empty($data['id']) ? $data['id'] : '';
    $key = !empty($data['key']) ? $data['key'] : '';
    $value = !empty($data['value']) ? $data['value'] : '';

    $valueArray = explode('|',$value);
    $newValue = $valueArray[0];
    $selectedMethod = $valueArray[1] ?? '';

    ?>
    <div class="section-body">
        <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-5">
                <div class="card profile-widget">

                    <div class="profile-widget-description">
                        <div class="">
                            {!! Form::open([
                                'route' => ['admin.important-setting.links.update', $id],
                                'id' => 'settingForm',
                                'method' => 'put',
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('key', __(Lang::get('forms.important-setting.key'))) !!}
                                            {!! Form::text('key', $key, [
                                                'class' => 'form-control' . ($errors->has('key') ? ' is-invalid' : ''),
                                                'readonly' => true,
                                                'placeholder' => __(Lang::get('forms.important-setting.key')),
                                            ]) !!}
                                            @error('key')
                                                <div class="invalid-feedback">
                                                    {{ $errors->get('key') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('value', __(Lang::get('forms.important-setting.value'))) !!}
                                            {!! Form::text('value', $value, [
                                                'class' => 'form-control' . ($errors->has('value') ? ' is-invalid' : ''),
                                                'placeholder' => __(Lang::get('forms.important-setting.value')),
                                            ]) !!}

                                            @error('value')
                                                <div class="invalid-feedback">
                                                    {{ $errors->get('value') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">{{ __(Lang::get('general.save')) }}</button>
                                <a href="{{ route('admin.important-setting.links.index') }}" class="btn btn-default">{{ __(Lang::get('general.cancel')) }}</a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#settingForm').validate({
            rules: {
                'field': {
                    required: true
                },
                'figure': {
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
