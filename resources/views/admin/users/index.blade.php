@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{!! asset('plugins/datatables/datatables.min.css') !!}">
    <style>
        .table-responsive button#show-profile {
            width: auto;
            margin: 5px;
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
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary">Add New</a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills mb-4" id="myTab3" role="tablist">
                        <li class="nav-item mr-3 mb-3">
                            <a class="nav-link active btn btn-primary filterButton" id="all-data" data-filter="all"
                               data-toggle="tab" href="#" role="tab" aria-controls="shop"
                               aria-selected="true">All</a>
                        </li>
                        <li class="nav-item mr-3 mb-3">
                            <a class="nav-link btn btn-primary filterButton" id="user-data" data-filter="user"
                               data-toggle="tab" href="#" role="tab" aria-controls="shop"
                               aria-selected="false">Normal User</a>
                        </li>
                        <li class="nav-item mr-3 mb-3 position-relative is-unread-comment">
                            <a class="nav-link btn btn-primary filterButton" id="user-data" data-filter="referred-user"
                               data-toggle="tab" href="javascript:void(0);" role="tab" aria-controls="shop"
                               aria-selected="false">Referred user
                                @if ($unreadReferralCount && $unreadReferralCount > 0)
                                    <span class="unread_referral_count">{{ $unreadReferralCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item mr-3 mb-3">
                            <a class="nav-link btn btn-primary filterButton" id="user-data" data-filter="admin_user"
                               data-toggle="tab" href="#" role="tab" aria-controls="shop"
                               aria-selected="false">Admin User</a>
                        </li>
                        <li class="nav-item mr-3 mb-3">
                            <a class="nav-link btn btn-primary filterButton" id="user-data" data-filter="support_user"
                               data-toggle="tab" href="#" role="tab" aria-controls="shop"
                               aria-selected="false">Support User</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade show active" id="allData" role="tabpanel"
                             aria-labelledby="all-data">
                            <div class="table-responsive">
                                <table class="table table-striped" id="all-table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email Address</th>
                                        <th>Phone Number</th>
                                        <th>SignUp date</th>
                                        <th>Last access</th>
                                        <th>Referral</th>
                                        <th>Action</th>
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
<div class="modal fade" id="editUsernameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="modal fade" id="show-referral" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="modal fade" id="editEmailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>

@section('scripts')
    <script>
        var allUserTable = "{{ route('admin.user.all.table') }}";
        var csrfToken = "{{ csrf_token() }}";
        var saveSignupCode = "{{ route('admin.user.signup-code.save') }}";
    </script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
    <script src="{!! asset('js/pages/users/users.js') !!}"></script>
@endsection
