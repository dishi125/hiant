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
<!--    <div class="section-header-button">
        <a href="{{ route('admin.category.add') }}" class="btn btn-primary">Add New</a>
    </div>-->
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade show active" id="allData" role="tabpanel" aria-labelledby="comment-data">
                            <div class="table-responsive">
                                <table class="table table-striped" id="News-website-table">
                                    <thead>
                                        <tr>
                                            <th>Website</th>
                                            <th>Order</th>
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
    <script src="{!! asset('plugins/jquery-ui/jquery-ui.js') !!}"></script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
    <script>
        var allTable = "{!! route('admin.news-websites.table') !!}";
        var csrfToken = "{{ csrf_token() }}";
        var updateOrder = "{!! route('admin.news-websites.update.order') !!}";

        $(function() {
            var allShop = $("#News-website-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                "order": [[ 1, "ASC" ]],
                ajax: {
                    url: allTable,
                    dataType: "json",
                    type: "POST",
                    data: { _token: csrfToken }
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('data-id', data.id).addClass('row1');
                },
                columns: [
                    { data: "website", orderable: true },
                    { data: "order", orderable: true },
                ]
            });

            $("#News-website-table > tbody").sortable({
                items: "tr",
                cursor: "move",
                opacity: 0.6,
                update: function () {
                    sendOrderToServer();
                },
            });
        });

        function sendOrderToServer() {
            var order = [];
            $("tr.row1").each(function (index, element) {
                order.push({
                    id: $(this).attr("data-id"),
                    position: index + 1,
                });
            });

            $.ajax({
                type: "POST",
                dataType: "json",
                url: updateOrder,
                data: {
                    order: order,
                    _token: csrfToken,
                },
                success: function (response) {
                    $("#News-website-table").dataTable().api().ajax.reload();
                },
            });
        }
    </script>
@endsection
