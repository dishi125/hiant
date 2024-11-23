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
                                            <th>Post</th>
                                            <th>Reason</th>
                                            <th>User Name</th>
                                            <th>Time</th>
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
<!-- Modal -->
<div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>

@section('scripts')
    <script>
        var allTable = "{!! route('admin.reported-post.table') !!}";
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
                    { data: "post_link", orderable: false },
                    { data: "reason", orderable: true },
                    { data: "user_name", orderable: true },
                    { data: "time", orderable: true },
                    { data: "action", orderable: false },
                ]
            });
        });

        function reportPost(id) {
            $.get(baseUrl + '/admin/reported-post/get/post/' + id, function (data, status) {
                $("#deletePostModal").html('');
                $("#deletePostModal").html(data);
                $("#deletePostModal").modal('show');
            });
        }

        $(document).on('click', '#blockPost', function(e) {
            var newsPostId = $(this).attr('news-post-id');
            $.ajax({
                url: baseUrl + "/admin/reported-post/block-post",
                method: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'news_post_id': newsPostId,
                },
                success: function (data) {
                    $("#deletePostModal").modal('hide');
                    $('#Reason-table').dataTable().api().ajax.reload();

                    if(data.status_code == 200) {
                        iziToast.success({
                            title: '',
                            message: data.message,
                            position: 'topRight',
                            progressBar: false,
                            timeout: 1000,
                        });
                    }else {
                        iziToast.error({
                            title: '',
                            message: data.message,
                            position: 'topRight',
                            progressBar: false,
                            timeout: 1000,
                        });
                    }
                }
            });
        });
    </script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
@endsection
