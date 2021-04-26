function chatlist_reload() {
    var csrfName = $('.csrf_token').attr("name");
    var csrfHash = $('.csrf_token').val();

    $.ajax({
        method: "POST",
        url: "/chatapp/user/chatlistreload",
        data: {
            [csrfName]: csrfHash
        },
        success: function (data) {
            $('.chats_list').html(data);
        }
    })
}

function chat_search(searchvalue) {
    var csrfName = $('.csrf_token').attr("name");
    var csrfHash = $('.csrf_token').val();

    $.ajax({
        url: "/chatapp/user/chatsearch",
        method: "post",
        data: {
            [csrfName]: csrfHash,
            searchvalue: searchvalue
        },
        success: function (data) {
            $('.chats_list').html(data);
        }
    })
}

$(document).ready(function () {
    $('.search_chats').keyup(function () {
        var searchvalue = $(this).val();
        if (searchvalue == "") {
            $('.search_i').show();
            $('.clear_search').hide();

            chatlist_reload();
        } else {
            $('.search_i').hide();
            $('.clear_search').show();

            chat_search(searchvalue);
        }
    });

    $(document).on('click', '.clear_search', function () {
        $('.search_chats').val("");
        $('.search_i').show();
        $('.clear_search').hide();

        chatlist_reload();
    });

    $(document).on('mouseover', '.incoming_msg', function () {
        var msgid = $(this).attr('msgid');
        $(this).children().css({
            'visibility': 'visible'
        });
    });

    $(document).on('mouseout', '.incoming_msg', function () {
        $(".incoming_msg_option").css('visibility', 'hidden');

        var msgid = $(this).attr('msgid');
        if ($(this).hasClass('show') == false) {
            $(this).children('div:nth-child(1)').css({
                'visibility': 'hidden'
            });
        } else {
            $(this).children('div:nth-child(1)').css({
                'visibility': 'visible'
            });
        }
    });

    $(document).on('mouseover', '.outgoing_msg', function () {
        var msgid = $(this).attr('msgid');
        $(this).children('div:nth-child(1)').css({
            'visibility': 'visible'
        });
    });

    $(document).on('mouseout', '.outgoing_msg', function () {
        $(".incoming_msg_option").css('visibility', 'hidden');

        var msgid = $(this).attr('msgid');
        if ($(this).hasClass('show') == false) {
            $(this).children('div:nth-child(1)').css({
                'visibility': 'hidden'
            });
        } else {
            $(this).children('div:nth-child(1)').css({
                'visibility': 'visible'
            });
        }
    });

    $(document).on('click', '.outgoing_delete', function () {
        var csrfName = $('.csrf_token').attr("name");
        var csrfHash = $('.csrf_token').val();
        var msgid = $(this).attr('msgid');
        var uidto = $(this).attr('uidto');
        var uidfrom = $(this).attr('uidfrom');

        var con = confirm('This message will be deleted!');
        if (con === true) {
            $.ajax({
                url: "/chatapp/user/outgoingdelete",
                method: "post",
                dataType: "json",
                data: {
                    [csrfName]: csrfHash,
                    msgid: msgid,
                    uidto: uidto,
                    uidfrom: uidfrom
                },
                success: function (data) {
                    $('.csrf_token').val(data.token);

                    chatlist_reload();

                    if (data.res === 'error') {

                    } else if (data.res === 'success') {
                        $('div[msgid=' + msgid + ']').remove();
                    }
                }
            })
        } else {
            return false;
        }
    });

    $(document).on('click', '.msginfo', function () {
        var csrfName = $('.csrf_token').attr("name");
        var csrfHash = $('.csrf_token').val();
        var msgid = $(this).attr('msgid');
        var uidto = $(this).attr('uidto');
        var uidfrom = $(this).attr('uidfrom');
        var currentuseruniqueid = $(".currentuseruniqueid").val();
        $('.message_body,.message_info_sentat,.message_info_read,.message_info_readat,.message_info_read').html("");

        $.ajax({
            url: '/chatapp/user/msginfo',
            method: "post",
            dataType: "json",
            data: {
                [csrfName]: csrfHash,
                msgid: msgid,
                uidto: uidto,
                uidfrom: uidfrom
            },
            success: function (data) {
                $('.csrf_token').val(data.token);

                if (data.res === 'error') {
                    window.location.reload();
                } else if (data.res === 'success') {
                    if (data['info']) {
                        $('.message_body').html(data['info'].msg);

                        $('.message_info_sentat').html(data['info'].sent_at);

                        if ((data['info'].msg_status == 0) && (data['info'].user_id_from == currentuseruniqueid)) {
                            $('.message_info_read').html('<i class="fas fa-check"></i>');
                        } else {
                            $('.message_info_read').html('--');
                        }

                        if ((data['info'].read_at == "") || (data['info'].read_at == null)) {
                            $('.message_info_readat').html("--");
                        } else {
                            $('.message_info_readat').html(data['info'].read_at);
                        }

                        $('.msginfo_modal').fadeIn();

                    } else {
                        window.location.reload();
                    }

                }
            }
        })
    });

    $(document).on('click', '.msginfo_modalclose', function () {
        $('.msginfo_modal').fadeOut();
    });
});