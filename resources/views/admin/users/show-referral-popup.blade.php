<div class="modal-dialog" style="max-width: 70%;">
    <div class="modal-content">
        <div class="modal-header justify-content-center">
            <h5>Referral Users</h5>
            @if($referralUser)
                <div class="ml-4 mt-1">
                    <strong>Name:</strong> {{$referralUser->display_name}}
                    <strong class="ml-2">Email:</strong> {{$referralUser->email}}
                    <strong class="ml-2">Number:</strong> <span class="copy_clipboard">{{$referralUser->mobile}}</span>
                </div>
            @endif
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body justify-content-center">
            <div class="align-items-center border-bottom d-flex mb-3 pb-3">
                <p class="mb-0 pr-3">Total Referral: {{ $cnt_referral }}</p>
            </div>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="referral-tab" data-toggle="tab" href="#referral-data" role="tab" aria-controls="referral" aria-selected="true">Referral Details</a>
                </li>
            </ul>

            <div class="row align-items-xl-center mb-3">
                <div class="w-100 tab-content" id="myTabContent2">
                    <div class="tab-pane fade show active" id="referral-data" role="tabpanel" aria-labelledby="referral-data">
                        <div class="table-responsive">
                            <table class="table table-striped" id="referral-data-table">
                                <thead>
                                    <tr>
                                        <th class="mr-3">Name</th>
                                        <th class="mr-3">Email</th>
                                        <th class="mr-3">Phone Number</th>
                                        <th class="mr-3">SignUp Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}} @if($user->deleted_at!=null) - <span style="color: deeppink;">Deleted</span> @endif</td>
                                        <td>{{$user->mobile}}</td>
                                        <td>{{ \App\Http\Controllers\Controller::formatDateTimeCountryWise($user->created_at, $adminTimezone)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $("#referral-data-table").DataTable({order: [[ 0, "desc" ]]});
</script>
