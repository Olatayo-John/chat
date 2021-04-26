<link href="<?php echo base_url('assets/css/chats.css'); ?>" rel="stylesheet">
<div class="content mr-3 mt-3">
    <input type="hidden" class="csrf_token" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">
    <input type="hidden" name="currentuseruniqueid" class="currentuseruniqueid" id="currentuseruniqueid" value="<?php echo $this->session->userdata("unique_id"); ?>" imagelocation="<?php echo base_url('assets/images/'); ?>" current_user_pimage="<?php echo base_url('assets/images/' . $this->session->userdata('p_image')) ?>">

    <div class="modal msginfo_modal" id="msginfo_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header message_body p-4">
                </div>
                <div class="modal-body">
                    <div class="message_info">
                        <div>
                            <span class="message_info_header">Sent at: </span><span class="message_info_sentat"></span>
                        </div>
                        <div>
                            <span class="message_info_header">Seen: </span><span class="message_info_read"></span>
                        </div>
                        <div>
                            <span class="message_info_header">Read at: </span><span class="message_info_readat"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-light msginfo_modalclose" style="background:#666">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- <?php echo $this->session->userdata('unique_id') ?> -->

    <div class="d-flex content_wrapper">
        <div class="col-sm-6 col-md-4 chats">
            <div class="chatheader">
                <h5>chats</h5>
            </div>
            <div class="search_div d-flex">

                <div class="col" style="margin: auto;text-align:center;">
                    <input type="text" name="search_chats" class="search_chats form-control" placeholder="Search to start a chat">
                    <span class="search_i"><i class="fas fa-search"></i> </span>
                    <span class="clear_search"><i class="fas fa-times"></i></span>
                </div>
            </div>

            <div class="chats_list">
                <?php if (count($chats) == 0) : ?>
                    <div class="no_chat text-danger">
                        <h6>You have no chats</h6>
                    </div>
                <?php elseif (count($chats) > 0) : ?>
                    <?php for ($i = 0; $i <= count($chats); $i++) { ?>
                        <?php if (!empty($chats[$i][0])) : ?>
                            <div class="chat" uniqueid="<?php echo $chats[$i][0]->unique_id ?>" u_id_from="<?php echo $chats[$i][0]->user_id_from ?>" u_id_to="<?php echo $chats[$i][0]->user_id_to ?>" msgid="<?php echo $chats[$i][0]->mid ?>">
                                <div class="chat_img">
                                    <img src="<?php echo base_url('assets/images/' . $chats[$i][0]->p_image); ?>" width="50px" height="50px">
                                </div>
                                <div class="chat_details">
                                    <div class="chat_details_up">
                                        <div class="chat_name">
                                            <h6><?php echo ucfirst($chats[$i][0]->fname) . " " . ucfirst($chats[$i][0]->lname) ?></h6>
                                        </div>
                                        <div class="chat_noti">
                                            <?php if (($chats[$i][0]->msg_status == '1') && ($chats[$i][0]->unique_id === $chats[$i][0]->user_id_from)) : ?>
                                                <i class="fas fa-envelope text-success"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="chat_details_down d-flex">
                                        <div class="chat_msg">
                                            <?php $wrd = $chats[$i][0]->msg; ?>
                                            <?php echo word_limiter($wrd, 6) ?>
                                        </div>
                                        <div class="chat_time">
                                            <div class=""><?php echo $chats[$i][0]->sent_time ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php } ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col messages pr-0 pl-0">
            <div class="chat_userdetails d-flex" style="display:none">
                <div class="chat_userimage" style="margin:10px 0 0 0">

                </div>
                <div class="chat_username ml-2">
                    <h6 class="m-0"></h6>
                    <div class="chat_userstatus">
                        <span class="active">Active Now<i class="fas fa-circle text-success ml-1"></i></span>
                        <span class="offline">Offline<i class="fas fa-circle text-danger ml-1"></i></span>
                    </div>
                </div>
            </div>
            <div class="messages_div" style="display:none">
            </div>
            <div class="send_msg pb-1" style="display:none">
                <textarea rows="2" name="msg" class="msg form-control" required placeholder="Enter your message..."></textarea>
                <button type="button" class="btn btn-outline-success msg_btn" uniqueid=""><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/chats.js') ?>"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.chat', function() {
            var csrfName = $('.csrf_token').attr("name");
            var csrfHash = $('.csrf_token').val();
            var uidfrom = $(this).attr("u_id_from");
            var uidto = $(this).attr("u_id_to");
            var uniqueid = $(this).attr("uniqueid");
            var msgid = $(this).attr("msgid");

            if ((uidfrom != "") && (uidto != "")) {
                $.ajax({
                    url: "<?php echo base_url("User/openchat") ?>",
                    method: "post",
                    dataType: "json",
                    data: {
                        [csrfName]: csrfHash,
                        uidfrom: uidfrom,
                        uidto: uidto,
                        uniqueid: uniqueid,
                        msgid: msgid
                    },
                    success: function(data) {
                        $('.csrf_token').val(data['token']);

                        var current_user_uniqueid = "<?php echo $this->session->userdata('unique_id'); ?> ";
                        var current_user_pimage = "<?php echo base_url('assets/images/' . $this->session->userdata('p_image')) ?>";
                        var imagelocation = "<?php echo base_url('assets/images/') ?>";

                        $(".messages_div,.chat_userimage").children().remove();

                        if (data['chats'].length > 0) {
                            $(".chat_userimage").append('<img src="' + imagelocation + data['chats'][0].p_image + '" width="50px" height="50px" style="border-radius:50%">');
                            $(".chat_username h6").text(data['chats'][0].fname + " " + data['chats'][0].lname);

                            if (data['chats'][0].status == '1') {
                                $("span.active").show();
                                $("span.offline").hide();
                            } else if (data['chats'][0].status == '0') {
                                $("span.active").hide();
                                $("span.offline").show();
                            }

                            for (let index = 0; index < data['chats'].length; index++) {

                                var newindex = parseInt(index) + 1;
                                var lmsg = (parseInt(newindex) == parseInt(data['chats'].length)) ? 'lmsg' : '';

                                if (parseInt(current_user_uniqueid) == (data['chats'][index].user_id_from)) {
                                    $(".messages_div").append('<div class="outgoing pb-2" data-toggle="tooltip" title="' + data['chats'][index].sent_time + '" msgid="' + data['chats'][index].mid + '" id="' + lmsg + '"><div class="outgoing_img"><img src="' + current_user_pimage + '" width="50px" height="50px"></div><div class="outgoing_msg" msgid="' + data['chats'][index].mid + '">' + data['chats'][index].msg + '<div class="outgoing_msg_option" data-toggle="dropdown" msgid="' + data['chats'][index].mid + '"><i class="fas fa-caret-down"></i></div><div class="dropdown-menu"><a class="dropdown-item text-dark outgoing_delete_me" msgid="' + data['chats'][index].mid + '" uidto="' + uniqueid + '" uidfrom="' + current_user_uniqueid + '" href="#">Delete message for me</a><a class="dropdown-item text-dark outgoing_delete" msgid="' + data['chats'][index].mid + '" uidto="' + uniqueid + '" uidfrom="' + current_user_uniqueid + '" href="#">Delete message</a><a class="dropdown-item text-dark msginfo" href="#" msgid="' + data['chats'][index].mid + '" uidto="' + uniqueid + '" uidfrom="' + current_user_uniqueid + '">Message info</a></div><div class="outgoing_msg_time text-danger">' + data['chats'][index].sent_time.slice(0, 5) + '</div></div></div></div>');
                                } else {
                                    $(".messages_div").append('<div class="incoming pb-2" data-toggle="tooltip" title="' + data['chats'][index].sent_time + '" msgid="' + data['chats'][index].mid + '" id="' + lmsg + '"><div class="incoming_img"><img src="' + imagelocation + data['chats'][index].p_image + '" width="50px" height="50px"></div><div class="incoming_msg" msgid="' + data['chats'][index].mid + '">' + data['chats'][index].msg + '<div class="incoming_msg_option" data-toggle="dropdown" msgid="' + data['chats'][index].mid + '"><i class="fas fa-caret-down"></i></div><div class="dropdown-menu"><a class="dropdown-item text-dark incoming_delete_me" msgid="' + data['chats'][index].mid + '" uidfrom="' + uniqueid + '" uidto="' + current_user_uniqueid + '" href="#">Delete message for me</a></div><div class="incoming_msg_time text-danger">' + data['chats'][index].sent_time.slice(0, 5) + '</div></div></div>');
                                }

                            }

                            $(".messages").children().show();

                            $('div[uniqueid="' + uniqueid + '"] div.chat_details div.chat_details_up div.chat_noti').css('visibility', 'hidden');

                            var elmnt = document.getElementById("lmsg");
                            elmnt.scrollIntoView();

                            $('.msg_btn').attr('uniqueid', uniqueid);

                            setInterval(function() {
                                message_reload();
                            }, 2000);
                        }
                    }
                });
            } else {
                window.location.reload();
            }
        });

        $(document).on('click', '.empty_chat', function() {
            var csrfName = $('.csrf_token').attr("name");
            var csrfHash = $('.csrf_token').val();
            var uniqueid = $(this).attr("uniqueid");
            var imagelocation = "<?php echo base_url('assets/images/') ?>";
            var u_pimage = $(this).attr("u_pimage");
            var u_name = $(this).attr("u_name");
            var u_status = $(this).attr("u_status");

            if (uniqueid != "") {
                $(".messages").children().show();

                $(".messages_div,.chat_userimage").children().remove();

                $(".chat_userimage").append('<img src="' + imagelocation + u_pimage + '" width="50px" height="50px" style="border-radius:50%">');
                $(".chat_username h6").text(u_name);

                if (u_status == '1') {
                    $("span.active").show();
                    $("span.offline").hide();
                } else if (u_status == '0') {
                    $("span.active").hide();
                    $("span.offline").show();
                }

                $(".messages_div").append('<div class="text-light text-center font-weight-bolder nomsg">No messages</div>');

                $('.msg_btn').attr('uniqueid', uniqueid);
            } else {
                window.location.reload();
            }
        });

        $(document).on('click', '.msg_btn', function() {
            var csrfName = $('.csrf_token').attr("name");
            var csrfHash = $('.csrf_token').val();
            var msg = $('.msg').val();
            var uidfrom = "<?php echo $this->session->userdata('unique_id') ?>";
            var uniqueid = $(this).attr("uniqueid");
            var current_user_pimage = "<?php echo base_url('assets/images/' . $this->session->userdata('p_image')) ?>";
            var timeasnow = "<?php echo date('h:i'); ?>";

            $(".nomsg").remove();

            if ((uidfrom != "") && (uniqueid != "") && (msg != "")) {
                $.ajax({
                    url: "<?php echo base_url("User/sendmsg") ?>",
                    method: "post",
                    dataType: "json",
                    data: {
                        [csrfName]: csrfHash,
                        uidfrom: uidfrom,
                        uniqueid: uniqueid,
                        msg: msg
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $('.csrf_token').val(data.token);

                        if (data.res == 'error') {
                            $('.msg').css('border', '1px solid #dc3545')
                        } else if (data.res == 'success') {
                            $('.messages_div div').removeAttr('id');

                            $(".messages_div").append('<div class="outgoing pb-2" msgid="' + data.return_id + '" id="lmsg"><div class="outgoing_img"><img src="' + current_user_pimage + '" width="50px" height="50px"></div><div class="outgoing_msg" msgid="' + data.return_id + '">' + data.msg + '<div class="outgoing_msg_option" data-toggle="dropdown" msgid="' + data.return_id + '"><i class="fas fa-caret-down"></i></div><div class="dropdown-menu"><a class="dropdown-item text-dark outgoing_delete_me" msgid="' + data.return_id + '" uidto="' + uniqueid + '" uidfrom="' + uidfrom + '" href="#">Delete message for me</a><a class="dropdown-item text-dark outgoing_delete" href="#" msgid="' + data.return_id + '" uidto="' + uniqueid + '" uidfrom="' + uidfrom + '">Delete message</a><a class="dropdown-item text-dark msginfo" href="#" msgid="' + data.return_id + '" uidto="' + uniqueid + '" uidfrom="' + uidfrom + '">Message info</a></div><div class="outgoing_msg_time text-danger">' + timeasnow + '</div></div></div>');
                        }

                        $('.msg').val("");

                        var elmnt = document.getElementById("lmsg");
                        elmnt.scrollIntoView();

                        // $('.search_chats').val("");
                        // $('.search_i').show();
                        // $('.clear_search').hide();
                    }
                });
            }
        });

    });
</script>