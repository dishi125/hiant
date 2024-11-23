<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header justify-content-center">
            <h5>Delete {{ $title }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body justify-content-center">
            <h6>Are you sure you want to delete this record?</h6>
        </div>
        <div class="modal-footer">
            <form method="POST" class="page_form" action="javascript::void(0)" data-id="{{ $id }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                @csrf
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <input type="button" value="Confirm" class="btn btn-danger pageForm" onclick="deletePage({{$id}});" />
            </form>
        </div>
    </div>
</div>
