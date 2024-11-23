@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{!! asset('css/custom.css') !!}">
@endsection
@section('header-content')
<h1>@if (@$title) {{ @$title }} @endif</h1>
@endsection

@section('content')
<?php
$data = (object)array();
if (!empty($keyword)) {
    $data = $keyword;
}
$id = !empty($data->id) ? $data->id : '' ;
$value= !empty($data->value) ? $data->value : '' ;
?>

<div class="section-body">
    <div class="row mt-sm-4">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card profile-widget">
                <div class="profile-widget-description">
                    <div class="">
                        @if (isset($keyword))
                        {!! Form::open(['route' => ['admin.keyword.update', $id], 'id' =>"keywordUpdateForm", 'method' => 'put', 'enctype' => 'multipart/form-data']) !!}
                        @else
                        <form id="KeywordForm" method="post" action="{{ route('admin.keyword.store') }}" enctype="multipart/form-data">
                        @endif
                        @csrf
                        <div class="card-body" id="card_body_div">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('keyword', __(Lang::get('forms.keyword.keyword'))) !!}
                                        {!! Form::text('value', $value, [
                                               'class' => 'form-control' . ($errors->has('value') ? ' is-invalid' : ''),
                                               'placeholder' => __(Lang::get('forms.keyword.keyword')),
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
                            <button type="submit" class="btn btn-primary" id="btn_submit">{{ __(Lang::get('general.save')) }}</button>
                            <a href="{{ route('admin.keyword.index')}}" class="btn btn-default">{{ __(Lang::get('general.cancel')) }}</a>
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
    $('#KeywordForm').validate({
        rules: {
            'value': {
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

    $('#keywordUpdateForm').validate({
        rules: {
            'value': {
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
