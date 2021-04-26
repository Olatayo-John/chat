<link href="<?php echo base_url('assets/css/edit.css'); ?>" rel="stylesheet">
<div class="content mr-3 mb-3 mt-3">
    <a href="<?php echo base_url('profile'); ?>"><i class="fas fa-chevron-circle-left mr-1 back"></i>Back to my profile</a>
</div>
<div class="content mr-3 mb-5 p-3 edit_form">
    <form method="post" action="<?php echo base_url('edit') ?>" enctype="multipart/form-data">
        <input type="hidden" class="csrf_token" name="<?php echo csrf_token() ?>" value="<?php echo csrf_hash() ?>">
        <div class="row">
            <div class="col form-group">
                <label for="fname"><span class="text-danger">* </span>First Name</label>
                <input type="text" name="fname" value="<?php echo $info->fname ?>" class="form-control" placeholder="Your Last name" required>
                <span class="text-danger font-weight-bolder"><?php echo form_error('fname'); ?></span>
            </div>
            <div class="col form-group">
                <label for="lname"><span class="text-danger">* </span>Last Name</label>
                <input type="text" name="lname" value="<?php echo $info->lname ?>" class="form-control" placeholder="Your Last name" required>
                <span class="text-danger font-weight-bolder"><?php echo form_error('lname'); ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="gender"><span class="text-danger">* </span>Gender</label>
            <select class="form-control" name="gender" required>
                <option value="male" <?php echo ($info->gender == 'male') ? 'selected' : '' ?>>Male</option>
                <option value="female" <?php echo ($info->gender == 'female') ? 'selected' : '' ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="p_image">Profile Image</label><i class="fas fa-question-circle ml-1" data-toggle="tooltip" title="max-width:2000px | max-height:2000px | max-size:5MB" style="cursor:pointer"></i><br>
            <input type="file" name="p_image">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo $info->email ?>" class="form-control" readonly disabled placeholder="example@domain.com" style="cursor:not-allowed">
        </div>
        <div class="form-group">
            <label for="about">About</label>
            <textarea rows="5" cols="0" name="about" class="form-control"><?php echo $info->about ?></textarea>
            <span class="text-danger font-weight-bolder"><?php echo form_error('about'); ?></span>
        </div>
        <div>
            <button type="submit" class="btn" style="background:white;color:#333"><i class="fas fa-save mr-1"></i>Update</button>
        </div>
    </form>
</div>