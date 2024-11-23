@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{!! asset('plugins/bootstrap-toggle/bootstrap4-toggle.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('plugins/datatables/datatables.min.css') !!}">
    <style>
        .table-responsive button#show-profile {
            width: auto;
            margin: 5px 5px 5px 0;
            white-space: normal;
        }

        .table-responsive .shops-date button#show-profile {
            width: 180px;
        }

        .table-responsive .shops-rate button#show-profile {
            width: 80px;
        }

        .table-responsive td span {
            margin: 5px;
        }
    </style>
@endsection

@section('header-content')
    <h1>
        @if (@$title)
            {{ @$title }}
        @endif
    </h1>
    <div class="section-header-button">
        <a href="{{ route('admin.category-posts.add') }}" class="btn btn-primary">Add Post</a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade show active" id="allData" role="tabpanel" aria-labelledby="comment-data">
                            <div class="table-responsive">
                                <table class="table table-striped" id="CategoryPost-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Agency</th>
                                            <th>Category</th>
                                            <th>Image</th>
                                            <th>Subcript</th>
                                            <th>Pin to top</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cover-spin"></div>
@endsection

@section('scripts')
    <script src="{!! asset('plugins/bootstrap-toggle/bootstrap4-toggle.min.js') !!}"></script>
    <script src="{!! asset('plugins/jquery-ui/jquery-ui.js') !!}"></script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
    <script>
        var allTable = "{!! route('admin.category-posts.table') !!}";
        var csrfToken = "{{ csrf_token() }}";
        var updatePinOnOff = "{!! route('admin.category-posts.update.pin-to-top') !!}";

        $(function() {
            var allShop = $("#CategoryPost-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                // "order": [[ 1, "ASC" ]],
                ajax: {
                    url: allTable,
                    dataType: "json",
                    type: "POST",
                    data: { _token: csrfToken },
                    dataSrc: function ( json ) {
                        setTimeout(function() {
                            $('.toggle-btn').bootstrapToggle();
                        }, 300);
                        return json.data;
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('data-id', data.id).addClass('row1');
                    $('.toggle-btn').bootstrapToggle();
                },
                columns: [
                    { data: "title", orderable: true },
                    { data: "agency", orderable: true },
                    { data: "category", orderable: true },
                    { data: "image", orderable: false },
                    { data: "subcript", orderable: true },
                    { data: "pin_to_top", orderable: false },
                ]
            });
        });

        $(document).on('change','.pintotop-toggle-btn',function(e){
            var dataID = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: updatePinOnOff,
                data: {
                    data_id: dataID,
                    checked: e.target.checked,
                    _token: csrfToken,
                },
                success: function (response) {
                    $("#CategoryPost-table").dataTable().api().ajax.reload();
                },
            });
        });
    </script>
@endsection
