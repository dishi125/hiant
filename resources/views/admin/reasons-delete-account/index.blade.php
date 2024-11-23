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
                                <table class="table table-striped" id="Reason-table">
                                    <thead>
                                        <tr>
                                            <th>User Name</th>
                                            <th>Phone Number</th>
                                            <th>Reason</th>
                                            <th>Requested At</th>
                                            <th>Signup Date</th>
                                            <th>Status</th>
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

    <!-- Modal -->
    <div class="modal fade" id="UserDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    </div>
@endsection

@section('scripts')
    <script>
        var allTable = "{!! route('admin.reasons-delete-account.table') !!}";
        var csrfToken = "{{ csrf_token() }}";

        $(function() {
            var allShop = $("#Reason-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                "order": [[ 3, "desc" ]],
                ajax: {
                    url: allTable,
                    dataType: "json",
                    type: "POST",
                    data: { _token: csrfToken }
                },
                columns: [
                    { data: "username", orderable: true },
                    { data: "phone", orderable: true },
                    { data: "reason", orderable: true },
                    { data: "request_date", orderable: true },
                    { data: "signup_date", orderable: true },
                    { data: "status", orderable: false },
                ]
            });
        });

        function removeUser(id) {
            var pageModel = $("#UserDeleteModal");

            $.get("{{ url('admin/reasons-delete-account/delete') }}" + "/" + id, function(data, status) {
                pageModel.html('');
                pageModel.html(data);
                pageModel.modal('show');
            });
        }
    </script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
@endsection
