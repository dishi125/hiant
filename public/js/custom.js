/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
checkIsCommentUnread();
setInterval(checkIsCommentUnread, 1000*30);
$('[data-toggle="tooltip"]').tooltip();
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});

var windows = {};

$('#update_language').on('change', function() {
    // From the other examples
    $.ajax({
        url: baseUrl + '/admin/user/update/language/'+$(this).attr('userid'),
        type: 'Post',
        data: {
            '_token': $("meta[name=csrf-token]").attr("content"),
            'status' : this.checked
        },
        success: function(data) {
            if(data.response == true){
                setTimeout(function(){
                    location.reload();
                }, 500);
            }
        }
    })
});
/*
$('#update_language').change(function() {
    $.ajax({
        url: baseUrl + '/admin/user/update/language/'+$(this).attr('userid'),
        type: 'Post',
        data: {
            '_token': $("meta[name=csrf-token]").attr("content"),
            'status' : $(this).prop('checked')
        },
        success: function(data) {
            if(data.response == true){
                setTimeout(function(){
                    location.reload();
                }, 500);
            }
        }
    })
}); */

$("textarea").keydown(function(e){
    if (e.keyCode == 13){
        e.preventDefault();
        var $this = $(this);
        var pos = $this[0].selectionStart;
        $this.val($this.val().substring(0, pos) + "\n" + $this.val().substring(pos));
    }
});

$(document).on("input", ".numeric", function() {
    this.value = this.value.replace(/\D/g,'');
});
$(document).on("input", ".decimal-input", function() {
    this.value = this.value.replace(/[^\d\.]/g,'');
});

function showToastMessage(message,isSuccess){
    if(iziToast){
        if(isSuccess == true){
            iziToast.success({
                title: '',
                message: message,
                position: 'topRight',
                progressBar: false,
                timeout: 1000,
            });
        }else {
            iziToast.error({
                title: '',
                message: message,
                position: 'topRight',
                progressBar: false,
                timeout: 1500,
            });
        }
    }
}

function checkIsCommentUnread(){
    console.log();

    $.ajax({
        method: 'POST',
        cache: false,
        data: {},
        url: baseUrl + "/admin/check/user/unread-comments",
        success: function(results) {

            $.each(results, function (key, val) {
                $('.'+key).text(val);
                if(val > 0){
                    $('.'+key).closest("li").addClass("is-unread-comment");
                }else{
                    $('.'+key).closest("li").removeClass("is-unread-comment");
                }
            });
            console.log("come")
        }
    });

}
function initializeApp(){

}


function copyTextLink(link,message = ''){
    let msg = message || 'Link';
    navigator.clipboard.writeText(link);
    showToastMessage(`The ${msg} is Copied`,true);
}

function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}


function closeWindow(name) {
    var window = windows[name];
    if(window) {
        window.close();
        delete windows[name];
    }
}

$('body').on('select2:open','select', function (e) {
    //var top = $('.select2-results__options').parent().parent().parent().css("top");
    if($('.select2-results__options').parent().parent().parent().hasClass('select2-container')){
        var offsetTop = $('.select2-results__options').parent().parent().parent().offset().top;
        var fromTop = $(window).scrollTop();
        var top = offsetTop - fromTop;
    }else{
        var top = 183;
    }
    if($('body').hasClass('modal-open') && top){
        $('.select2-results__options').css('max-height',`calc(100vh - (60px + ${top}px))`);
    }
});

$(document).on('click', '.copy_clipboard', function (){
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).text()).select();
    $temp.focus();
    document.execCommand("copy");
    $temp.remove();
    // alert("Phone number is copied.");
    showToastMessage("Phone number is copied.",true);
})

$(document).on('click', '#deleteUserDetail', function(e) {
    var userId = $(this).attr('user-id');
    $.ajax({
        url: baseUrl + "/admin/delete/user/details",
        method: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'userId': userId,
        },
        success: function (data) {
            if($('#deletePostModal').length){
                $("#deletePostModal").modal('hide');
            }else if($('#deleteModal').length){
                $("#deleteModal").modal('hide');
            }else if($('#editModal').length){
                $("#editModal").modal('hide');
            }

            if($('#all-comment-table').length){
                $('#all-comment-table').dataTable().api().ajax.reload();
            }else{
                $('#all-table').dataTable().api().ajax.reload();
            }

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

function editEmail(url){
    $.get(url, function (data, status) {
        $("#editEmailModal").html('');
        $("#editEmailModal").html(data);
        $("#editEmailModal").modal('show');
    });
}

$(document).on("submit","#editEmailForm",function(e){
    e.preventDefault();
    var ajaxurl = $(this).attr('action');

    $.ajax({
        method: 'POST',
        cache: false,
        data: $(this).serialize(),
        url: ajaxurl,
        success: function(results) {
            $(".cover-spin").hide();

            $('#all-table').DataTable().destroy();
            loadTableData('all');
            if(results.success == true) {
                iziToast.success({
                    title: '',
                    message: results.message,
                    position: 'topRight',
                    progressBar: false,
                    timeout: 1000,
                });
            }else {
                iziToast.error({
                    title: '',
                    message: results.message,
                    position: 'topRight',
                    progressBar: false,
                    timeout: 2000,
                });
            }
            $("#editEmailModal").modal('hide');

        },
        beforeSend: function(){ $(".cover-spin").show(); },
        error: function(response) {
            $(".cover-spin").hide();
            if( response.responseJSON.success === false ) {
                var errors = response.responseJSON.errors;

                $.each(errors, function (key, val) {
                    console.log(val);
                    var errorHtml = '<label class="error">'+val+'</label>';
                    $('#'+key).parent().append(errorHtml);
                });
            }
        }
    });
});
