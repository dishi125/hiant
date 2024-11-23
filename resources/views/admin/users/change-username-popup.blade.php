<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header justify-content-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-footer">
            <form id="edituserForm" style="width: 100%;" method="POST" action="{{route('admin.user.edit-username',$id)}}" accept-charset="UTF-8">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mt-3">
                            <label for="">User name</label>
                            <input type="text" class="form-control" value="{{$user_detail->name}}" name="username" placeholder="User Name" required id="username" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Gender</label>
                            <select class="form-control" name="gender" id="gender">
                                <option value="1" @if($user_detail->gender=="1") selected @endif>Man</option>
                                <option value="2" @if($user_detail->gender=="2") selected @endif>Women</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                <button type="submit" id="form_submit" class="btn btn-primary">Confirm</button>
            </form>
        </div>
    </div>
</div>
