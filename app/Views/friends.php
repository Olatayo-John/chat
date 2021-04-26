<link href="<?php echo base_url('assets/css/friends.css'); ?>" rel="stylesheet">
<div class="content mr-3 mb-5 mt-3">
    <input type="hidden" class="csrf_token" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">

    <div class="search_div d-flex">
        <div class="col">
            <h3>friends <?php if ($friends->num_rows() > '0') {
                            echo "(" . $friends->num_rows() . ")";
                        } ?></h3>
        </div>
        <?php if ($friends->num_rows() > '0') : ?>
            <div class="col" style="margin: auto;text-align:center;">
                <input type="text" name="search_frnds" class="search_frnds form-control text-center">
                <span class="search_i"><i class="fas fa-search"></i></span>
                <span class="clear_search"><i class="fas fa-times"></i></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- <div class='add_friend mt-3'>
        <i class="fas fa-plus-circle"></i>
    </div> -->

    <?php if ($friends->num_rows() == '0') : ?>
        <div class='no_friend mt-3 text-danger'>
            You have no friends
        </div>
    <?php endif; ?>

    <?php if ($friends->num_rows() > '0') : ?>
        <div class="mt-3 friends_list">
            <?php foreach ($friends->result_array() as $friend) : ?>
                <div class='friend'>
                    <div class="friend_details">
                        <a href=""><img src="<?php echo base_url('assets/images/' . $friend['p_image']) ?>"></a>
                    </div>
                    <div class="friend_details">
                        <a href="" class="friend_name"><?php echo $friend['fname'] . ' ' . $friend['lname'] ?></a>
                        <?php if ($friend['status'] == '1') : ?>
                            <i class="fas fa-circle text-success"></i>
                        <?php elseif ($friend['status'] == '0') : ?>
                            <i class="fas fa-circle text-danger"></i>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="<?php echo base_url('assets/js/friends.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('.search_frnds').keyup(function() {
            var searchvalue = $(this).val();
            if (searchvalue == "") {
                $('.search_i').show();
                $('.clear_search').hide();

                friendlist_reload();
            } else {
                $('.search_i').hide();
                $('.clear_search').show();

                friend_search(searchvalue);
            }
        });

        function friendlist_reload() {
            var csrfName = $('.csrf_token').attr("name");
            var csrfHash = $('.csrf_token').val();

            $.ajax({
                method: "POST",
                url: "<?php echo base_url('user/friendlist_reload') ?>",
                data: {
                    [csrfName]: csrfHash
                },
                success: function(data) {
                    $('.friends_list').html(data);
                }
            })
        }

        function friend_search(searchvalue) {
            var csrfName = $('.csrf_token').attr("name");
            var csrfHash = $('.csrf_token').val();

            $.ajax({
                url: "<?php echo base_url('user/friend_search'); ?>",
                method: "post",
                data: {
                    [csrfName]: csrfHash,
                    searchvalue: searchvalue
                },
                // dataType: "json",
                success: function(data) {
                    $('.friends_list').html(data);
                    // $('.csrf_token').val(data.token);
                }
            })
        }

        $(document).on('click', '.clear_search', function() {
            $('.search_frnds').val("");
            $('.search_i').show();
            $('.clear_search').hide();

            friendlist_reload();
        });
    });
</script>