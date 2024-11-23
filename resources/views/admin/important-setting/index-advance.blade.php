@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{!! asset('plugins/datatables/datatables.min.css') !!}">
@endsection

@section('header-content')
    <h1>@if (@$title) {{ @$title }} @endif</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @include('admin.important-setting.common-setting-menu', ['active' => 'advance'])
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="advance-table">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>value</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cover-spin"></div>
    <!-- Modal -->

@endsection

@section('scripts')
    <script>
        var advanceTable = "{!! route('admin.important-setting.advance.table') !!}";
        var csrfToken = "{{csrf_token()}}";
    </script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
    <script>
        $(function() {
            var allData = $("#advance-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                ajax: {
                    url: advanceTable,
                    dataType: "json",
                    type: "POST",
                    data: { _token: csrfToken}
                },
                columns: [
                    { data: "title", orderable: false },
                    { data: "value", orderable: false },
                    { data: "action", orderable: false }
                ]
            });

        });
    </script>
@endsection

@section('page-script')
@endsection
