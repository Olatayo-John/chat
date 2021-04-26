<link href="<?php echo base_url('assets/css/profile.css'); ?>" rel="stylesheet">
<div class="content mr-3 mb-5 mt-3">
    <div class="user_name" style="display:flex;justify-content:space-between">
        <h4 class="m-0"><?php echo ucfirst($this->session->userdata('fname')) . ' ' . ucfirst($this->session->userdata('lname')) ?></h4>
        <a href="<?php echo base_url('edit'); ?>" style="margin:auto 0;"><i class="fas fa-pencil-alt edit_profie"></i></a>
    </div>

    <div class="d-flex mt-3 profile_details">
        <div class="col mr-1">
            <h5 class="mt-2 mb-0">Profile</h5>
            <hr>
            <div class="d-flex">
                <div>
                    <img src="<?php echo base_url('assets/images/') . $this->session->userdata('p_image'); ?>" width="200px" height="200px" style="border-radius: 50%">
                </div>
                <!-- <?php if ($this->session->userdata('p_image') !== 'male.png' && $this->session->userdata('p_image') !== 'female.png') : ?>
                    <div class="text-danger remove_pimage">
                        <i class="fas fa-times"></i>
                    </div>
                <?php endif; ?> -->
            </div>
            <div class="mt-3 mb-3">
                <div>
                    <?php if ($this->session->userdata('status') == '1') : ?>
                        <i class="mr-2 fas fa-circle text-success"></i>Active Now
                    <?php elseif ($this->session->userdata('status') == '0') : ?>
                        <i class="mr-2 fas fa-circle text-warning"></i>Offline
                    <?php endif; ?>
                </div>
                <div>
                    <i class="fas fa-envelope mr-2"></i><a style="color:white" href="mailto:<?php echo $this->session->userdata('email') ?>"><?php echo $this->session->userdata('email') ?></a>
                </div>
            </div>

        </div>
        <!-- <div class="col ml-1">
            <h5 class="mt-2 mb-0">About</h5>
            <hr>
        </div> -->
    </div>

    <div class="about mt-3 p-3">
        <h5 class="mt-2 mb-0">About</h5>
        <hr>
        <span><?php echo $info->about ?></span>
    </div>
</div>

<script src="<?php echo base_url('assets/js/profile.js') ?>"></script>