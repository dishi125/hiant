@extends('layouts.app')

@section('styles')
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
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade show active" id="allData" role="tabpanel" aria-labelledby="comment-data">
                            <div class="table-responsive">
                                <table class="table table-striped" id="Group-chat-table">
                                    <thead>
                                        <tr>
                                            <th>Group name</th>
                                            <th>Leader name</th>
                                            <th>Created At</th>
                                            <th>total participants</th>
                                            <th></th>
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

<div class="modal fade" id="show-messages" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

@section('scripts')
    <script src="{!! asset('plugins/jquery-ui/jquery-ui.js') !!}"></script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
    <script>
        var allTable = "{!! route('admin.group-chat.table') !!}";
        var csrfToken = "{{ csrf_token() }}";

        $(function() {
            var allShop = $("#Group-chat-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                "order": [[ 2, "DESC" ]],
                ajax: {
                    url: allTable,
                    dataType: "json",
                    type: "POST",
                    data: { _token: csrfToken }
                },
                columns: [
                    { data: "group_title", orderable: true },
                    { data: "leader", orderable: true },
                    { data: "created_at", orderable: true },
                    { data: "participants", orderable: false },
                    { data: "see_more", orderable: false },
                ]
            });
        });

        function viewEntireChat(group_id){
            $.get(baseUrl + '/admin/group-chat/show-messages/' + group_id, function (data, status) {
                $('#show-messages').html('');
                $('#show-messages').html(data);
                $('#show-messages').modal('show');
            });
        }
    </script>
@endsection
