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
                        <form id="CategoryPostForm" method="post" action="{{ route('admin.category-posts.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body" id="card_body_div">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select class="form-control" name="category" id="category">
                                            <option selected disabled>Select category</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                        <div class="invalid-feedback">
                                            {{ $errors->get('category') }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input class="form-control {{$errors->has('title') ? 'is-invalid' : ''}}" placeholder="Title" name="title" type="text" id="title">
                                        @error('title')
                                        <div class="invalid-feedback">
                                            {{ $errors->get('title') }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="agency_name">Agency name</label>
                                        <input class="form-control {{$errors->has('agency_name') ? ' is-invalid' : ''}}" placeholder="Agency name" name="agency_name" type="text" id="agency_name">
                                        @error('agency_name')
                                        <div class="invalid-feedback">
                                            {{ $errors->get('agency_name') }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input class="form-control {{$errors->has('image') ? ' is-invalid' : ''}}" placeholder="Image" name="image" type="file" id="image">
                                        @error('image')
                                        <div class="invalid-feedback">
                                            {{ $errors->get('image') }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="subcript">Subcript</label>
                                        <input class="form-control {{$errors->has('subcript') ? ' is-invalid' : ''}}" placeholder="Subcript" name="subcript" type="text" id="subcript">
                                        @error('subcript')
                                        <div class="invalid-feedback">
                                            {{ $errors->get('subcript') }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary" id="btn_submit">{{ __(Lang::get('general.save')) }}</button>
                            <a href="{{ route('admin.category-posts.index')}}" class="btn btn-default">{{ __(Lang::get('general.cancel')) }}</a>
                        </div>

                        </form>
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
    $('#CategoryPostForm').validate({
        rules: {
            'category': {
                required: true
            },
            'title': {
                required: true
            },
            'agency_name': {
                required: true
            },
            'image': {
                required: true,
                accept: "image/jpg,image/jpeg,image/png,image/gif"
            },
            'subcript': {
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
