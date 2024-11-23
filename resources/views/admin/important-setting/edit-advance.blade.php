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
    if (!empty($advance)) {
        $data = $advance;
    }

    $id = !empty($data['id']) ? $data['id'] : '';
    $title = !empty($data['title']) ? $data['title'] : '';
    $is_show = $data['is_show'];
    ?>
    <div class="section-body">
        <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-5">
                <div class="card profile-widget">

                    <div class="profile-widget-description">
                        <div class="">
                            {!! Form::open([
                                'route' => ['admin.important-setting.advance.update', $id],
                                'id' => 'settingForm',
                                'method' => 'put',
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('title', __(Lang::get('forms.important-setting.title'))) !!}
                                            {!! Form::text('title', $title, [
                                                'class' => 'form-control' . ($errors->has('title') ? ' is-invalid' : ''),
                                                'readonly' => true,
                                                'placeholder' => __(Lang::get('forms.important-setting.title')),
                                            ]) !!}
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $errors->get('title') }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('is_show', __(Lang::get('forms.important-setting.is_show'))) !!}
                                            <select name="is_show" id="is_show" class="form-control {{$errors->has('is_show') ? ' is-invalid' : ''}}">
                                                <option value="1" @if($is_show==1) selected @endif>Show</option>
                                                <option value="0" @if($is_show==0) selected @endif>Hide</option>
                                            </select>

                                            @error('is_show')
                                            <div class="invalid-feedback">
                                                {{ $errors->get('is_show') }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">{{ __(Lang::get('general.save')) }}</button>
                                <a href="{{ route('admin.important-setting.advance.index') }}" class="btn btn-default">{{ __(Lang::get('general.cancel')) }}</a>
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
                'title': {
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
