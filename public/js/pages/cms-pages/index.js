$(function() {
    $("#all-table").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        deferRender: true,
        "order": [[ 0, "asc" ]],
        ajax: {
            url: getJson,
            dataType: "json",
            type: "POST",
            data: { _token: csrfToken }
        },
        columns: [
            { data: "name", orderable: true },
            { data: "date", orderable: true },
            { data: "actions", orderable: false },
        ]
    });
});

function deletePageConfirmation(page_id){
    $.get(baseUrl + '/admin/policy-pages/get/delete/' + page_id, function (data, status) {
        $("#deletePageModal").html('');
        $("#deletePageModal").html(data);
        $("#deletePageModal").modal('show');
    });
}

function deletePage(page_id){
    if(page_id){
        $.ajax({
            url: baseUrl + "/admin/policy-pages/delete",
            method: 'POST',
            data: {
                _token: csrfToken,
                page_id : page_id,
            },
            beforeSend: function(){ $(".cover-spin").show(); },
            success: function(response) {
                $(".cover-spin").hide();
                $("#deletePageModal").modal('hide');
                if(response.success == true){
                    showToastMessage(response.message,true);
                    $('#all-table').DataTable().ajax.reload();
                }else {
                    showToastMessage(response.message,false);
                }
            }
        });
    }
}
