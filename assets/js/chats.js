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

function message_reload() {
    var csrfName = $('.csrf_token').attr("name");
    var csrfHash = $('.csrf_token').val();
    var imagelocation = $(".currentuseruniqueid").attr("imagelocation");
    var current_user_pimage = $(".currentuseruniqueid").attr("current_user_pimage");
    var uidfrom = $(".currentuseruniqueid").val();
    var uniqueid = $(".msg_btn").attr("uniqueid");

    $.ajax({
        method: "POST",
        url: "/chatapp/user/messagereload",
        data: {
            [csrfName]: csrfHash,
            uidfrom: uidfrom,
            uniqueid: uniqueid,
        },
        dataType: "json",
        success: function (data) {
            $('.csrf_token').val(data.token);

            if (data['userstatus'].length > 0) {
                if (data['userstatus'][0].status == '1') {
                    $("span.active").show();
                    $("span.offline").hide();
                } else if (data['userstatus'][0].status == '0') {
                    $("span.active").hide();
                    $("span.offline").show();
                }
            }

            if (data['info'].length > 0) {
                for (let index = 0; index < data['info'].length; index++) {
                    // console.log(data['info'][index].mid);

                    var newindex = parseInt(index) + 1;
                    var lmsg = (parseInt(newindex) == parseInt(data['info'].length)) ? 'lmsg' : '';
                    var msgid_exist = $("div.messages_div").find("div[msgid='" + data['info'][index].mid + "']");
                    $("div.messages_div").find("div#lmsg").removeAttr('id');

                    if (msgid_exist.length == 0) {
                        $(".messages_div").append('<div class="incoming pb-2" data-toggle="tooltip" title="' + data['info'][index].sent_time + '" msgid="' + data['info'][index].mid + '" id="' + lmsg + '"><div class="incoming_img"><img src="' + imagelocation + data['info'][index].p_image + '" width="50px" height="50px"></div><div class="incoming_msg" msgid="' + data['info'][index].mid + '">' + data['info'][index].msg + '<div class="incoming_msg_option" data-toggle="dropdown" msgid="' + data['info'][index].mid + '"><i class="fas fa-caret-down"></i></div><div class="dropdown-menu"><a class="dropdown-item text-dark incoming_delete_me" msgid="' + data['info'][index].mid + '" uidfrom="' + uniqueid + '" uidto="' + uidfrom + '" href="#">Delete message for me</a></div><div class="incoming_msg_time text-danger">' + data['info'][index].sent_time.slice(0, 5) + '</div></div></div>');

                        var elmnt = document.getElementById("lmsg");
                        elmnt.scrollIntoView();
                    }
                }
            }
        }
    })
}

function pusher() {
    var csrfName = $('.csrf_token').attr("name");
    var csrfHash = $('.csrf_token').val();
    $.ajax({
        url: "/chatapp/user/pusher",
        method: "post",
        dataType: "json",
        data: {
            [csrfName]: csrfHash
        }
    })
}

function clearintervals() {
    for (let index = 0; index < 100; index++) {
        clearInterval(index);
    }
}

function setchatinterval() {
    setInterval(function () {
        chatlist_reload();
    }, 2000);
}

$(document).ready(function () {
    setchatinterval();

    // Pusher.logToConsole = true;
    var pusher = new Pusher('0e610562daa4fcb70499', {
        cluster: 'eu'
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function (data, metadata) {
        $(".ajax_succ_div").fadeIn();
        $(".ajax_res_succ").html((parseInt(data.length) > 50) ? "<strong>New message</strong><br>" + data.substring(0, 50) + "..." : "<strong>New message</strong><br>" + data);
    });

    $('.search_chats').keyup(function () {
        var searchvalue = $(this).val();
        if (searchvalue == "" || searchvalue == undefined || searchvalue == null) {
            $('.search_i').show();
            $('.clear_search').hide();

            setchatinterval();

        } else {
            clearintervals();

            $('.search_i').hide();
            $('.clear_search').show();

            chat_search(searchvalue);
        }
    });

    $(document).on('click', '.clear_search', function () {
        $('.search_chats').val("");
        $('.search_i').show();
        $('.clear_search').hide();

        setchatinterval();
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

                    // chatlist_reload();

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