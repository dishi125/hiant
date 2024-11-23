<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header justify-content-center">
            <h5>Delete User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body justify-content-center">
            <h6>Are you sure you want to delete this User?</h6>
        </div>
        <div class="modal-footer">
            <form method="POST" action="{{route('admin.reasons-delete-account.destroy',$id)}}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                @csrf
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Confirm</button>
            </form>
        </div>
    </div>
</div>
