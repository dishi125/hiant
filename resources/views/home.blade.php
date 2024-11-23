@extends('layouts.app')
@section('header-content')
<h1>@if (@$title) {{ @$title }} @endif</h1>
@endsection
@section('content')
<!-- Main Content -->
<div class="main-container">
</div>
@endsection
@section('page-script')
<script src="{!! asset('js/chart.min.js') !!}"></script>
@endsection
