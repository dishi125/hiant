$(function() {
    var allData = $("#links-table").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        deferRender: true,
        ajax: {
            url: linksTable,
            dataType: "json",
            type: "POST",
            data: { _token: csrfToken}
        },
        columns: [
            { data: "field", orderable: true },
            { data: "figure", orderable: true },
            { data: "actions", orderable: false }
        ]
    });

});
