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
if (!empty($category)) {
    $data = $category;
}
$id = !empty($data->id) ? $data->id : '' ;
$category_name= !empty($data->category) ? $data->category : '' ;
?>

<div class="section-body">
    <div class="row mt-sm-4">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card profile-widget">
                <div class="profile-widget-description">
                    <div class="">
                        @if (isset($category))
                        {!! Form::open(['route' => ['admin.category.update', $id], 'id' =>"categoryUpdateForm", 'method' => 'put', 'enctype' => 'multipart/form-data']) !!}
                        @else
                        <form id="CategoryForm" method="post" action="{{ route('admin.category.store') }}" enctype="multipart/form-data">
                        @endif
                        @csrf
                        <div class="card-body" id="card_body_div">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('category', __(Lang::get('forms.category.category'))) !!}
                                        {!! Form::text('category', $category_name, [
                                               'class' => 'form-control' . ($errors->has('category') ? ' is-invalid' : ''),
                                               'placeholder' => __(Lang::get('forms.category.category')),
                                       ]) !!}

                                        @error('value')
                                        <div class="invalid-feedback">
                                            {{ $errors->get('category') }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary" id="btn_submit">{{ __(Lang::get('general.save')) }}</button>
                            <a href="{{ route('admin.category.index')}}" class="btn btn-default">{{ __(Lang::get('general.cancel')) }}</a>
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
    $('#CategoryForm').validate({
        rules: {
            'category': {
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

    $('#categoryUpdateForm').validate({
        rules: {
            'category': {
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
