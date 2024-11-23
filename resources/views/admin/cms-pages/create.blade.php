@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{!! asset('plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') !!}">
<link rel="stylesheet" href="{!! asset('plugins/bootstrap-datepicker/bootstrap.min.css') !!}">

<style>
    #pageForm textarea.form-control {
        min-height: 100px !important;
    }
    .ck-editor__editable_inline {
        min-height: 400px;
    }

</style>
@endsection

@section('header-content')
<h1>{{ @$title }}</h1>
@endsection

<?php
$id = (isset($page)) ? $page->id : '';
?>

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card profile-widget">
            <div class="profile-widget-description">
                    @if($id)
                        {!! Form::open(['route' => ['admin.policy-pages.update',$id], 'id' =>"pageForm", 'enctype' => 'multipart/form-data']) !!}
                    @else
                        {!! Form::open(['route' => 'admin.policy-pages.store', 'id' =>"pageForm", 'enctype' => 'multipart/form-data']) !!}
                    @endif
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('title', __('Title')); !!}
                                {!! Form::text('title', $page->title ?? '', ['required' => true,'id' => 'title', 'class' => 'required form-control', 'placeholder' => __('Title') ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('content', __('Content')); !!}
                                {!! Form::textarea('content', $page->content ?? '', ['class' => 'ckeditor form-control', 'placeholder' => __('Content') ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <button type="submit"
                            class="btn btn-primary mr-2">{{ __(Lang::get('general.save')) }}</button>
                        <a href="{{ route('admin.important-setting.policy-pages.index')}}"
                            class="btn btn-default">{{ __(Lang::get('general.cancel')) }}</a>
                    </div>
                    {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="cover-spin"></div>
@endsection

@section('scripts')
<script src="{{asset('plugins/editor/ckeditor.js')}}"></script>
<script>
    var csrfToken = "{{csrf_token()}}";

    $(function() {
        /* ClassicEditor
            .create( document.querySelector( '.ckeditor' ) )
        .catch( error => {
            console.error( error );
        }); */

        ClassicEditor
            .create( document.querySelector( '.ckeditor' ), {
                ckfinder: {
                    uploadUrl: '{{route('admin.ckeditor.upload').'?_token='.csrf_token()}}',
                    //uploadUrl: '{{route('admin.ckeditor.upload')."?command=QuickUpload&type=Files&responseType=json"}}',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                }
            },{
                alignment: {
                    options: [ 'right', 'right' ]
                }} )
            .then( editor => {
                console.log( editor );
            })
            .catch( error => {
                console.error( error );
            });

        $(document).on('submit',"#pageForm",function(event){
            event.preventDefault();
            $('label.error').remove();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                type:"POST",
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function() {
                    $('.cover-spin').show();
                },
                success:function(response) {
                    $('.cover-spin').hide();
                    if(response.success == true){
                        showToastMessage(response.message,true);

                        setTimeout(function(){
                            window.location.href = response.redirect;
                        },1000);

                    }else {
                        showToastMessage(response.message,false);
                    }
                },
                error:function (response, status) {
                    $('.cover-spin').hide();
                    showToastMessage('Page has not been created successfully.',false);
                }
            });
        });
    });
</script>
@endsection
