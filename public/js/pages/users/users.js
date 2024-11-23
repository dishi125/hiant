var filter;
var is_referral = false;
$(function () {
    loadTableData('all');

    $('body').on('click', '.filterButton', function () {
        filter = $(this).attr('data-filter');

        if(filter == 'referred-user'){
            is_referral = true;
        }
        if(filter != 'referred-user' && is_referral == true){
            $('.unread_referral_count').hide();
        }

        $('#all-table').DataTable().destroy();
        loadTableData(filter);
    })

    $(document).on('click','#btn_save_signup_code', function (){
        var user_id = $(this).attr('user-id');
        var signup_code = $(this).siblings('#signup_code').val();
        if (signup_code==""){
            showToastMessage("Please enter code.", false);
        }
        else {
            $.ajax({
                url: saveSignupCode,
                method: 'POST',
                data: {
                    'user_id': user_id,
                    'signup_code': signup_code,
                },
                beforeSend: function () {
                    $('.cover-spin').show();
                },
                success: function (data) {
                    $('.cover-spin').hide();
                    showToastMessage(data.message, data.success);
                    if (data.success == true) {
                        $('#all-table').DataTable().destroy();
                        loadTableData(filter);
                    }
                }
            });
        }
    })
});

function loadTableData(filter) {
    var filter = filter || 'all';

    var allHospital = $("#all-table").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        deferRender: true,
        "lengthMenu": [25, 50, 100, 200, 500],
        "pageLength": 100,
        "order": [
            [3, "desc"]
        ],
        ajax: {
            url: allUserTable,
            dataType: "json",
            type: "POST",
            data: {
                _token: csrfToken,
                filter: filter,
            }
        },
        columns: [{
                data: "name",
                orderable: true
            },
            {
                data: "email",
                orderable: true
            },
            {
                data: "phone",
                orderable: false
            },
            {
                data: "signup",
                orderable: true
            },
            {
                data: "last_access",
                orderable: true
            },
            {
                data: "referral",
                orderable: false
            },
            {
                data: "actions",
                orderable: false
            }
        ]
    });
}

function deleteUser(id) {
    $.get(baseUrl + '/admin/users/get/account/' + id, function (data, status) {
        $("#deletePostModal").html('');
        $("#deletePostModal").html(data);
        $("#deletePostModal").modal('show');
    });
}

function editPassword(id) {
    $.get(baseUrl + '/admin/users/get/edit/account/' + id, function (data, status) {
        $("#deletePostModal").html('');
        $("#deletePostModal").html(data);
        $("#deletePostModal").modal('show');
    });
}

$(document).on('click', '.copy_code', function (){
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).text()).select();
    $temp.focus();
    document.execCommand("copy");
    $temp.remove();
    // alert("Phone number is copied.");
    showToastMessage("Referral code is copied.",true);
})

function editUsername(url){
    $.get(url, function (data, status) {
        $("#editUsernameModal").html('');
        $("#editUsernameModal").html(data);
        $("#editUsernameModal").modal('show');
    });
}

$(document).on("submit","#edituserForm",function(e){
    e.preventDefault();
    var ajaxurl = $(this).attr('action');

    $.ajax({
        method: 'POST',
        cache: false,
        data: $(this).serialize(),
        url: ajaxurl,
        success: function(results) {
            $(".cover-spin").hide();
            if(results.success == true) {
                iziToast.success({
                    title: '',
                    message: results.message,
                    position: 'topRight',
                    progressBar: false,
                    timeout: 1000,
                });
                $('#all-table').DataTable().destroy();
                loadTableData('all');
            }else {
                iziToast.error({
                    title: '',
                    message: results.message,
                    position: 'topRight',
                    progressBar: false,
                    timeout: 2000,
                });
            }
            $("#editUsernameModal").modal('hide');

        },
        beforeSend: function(){ $(".cover-spin").show(); },
        error: function(response) {
            $(".cover-spin").hide();
            if( response.responseJSON.success == false ) {
                var error = response.responseJSON.message;
                showToastMessage(error,false);
            }
        }
    });
});

function showReferralDetail(id){
    $.get(baseUrl + '/admin/show/referral/users/' + id, function (data, status) {
        $('#show-referral').html('');
        $('#show-referral').html(data);
        $('#show-referral').modal('show');
    });
}
