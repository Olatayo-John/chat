<link href="<?php echo base_url('assets/css/register.css'); ?>" rel="stylesheet">
<div class="content mr-3 mb-5 mt-5">
    <form method="post" action="register" enctype="multipart/form-data" class="container col-md-6">
        <input type="hidden" class="csrf_token" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">
        <h4>Registeration</h4>
        <div class="row">
            <div class="col form-group">
                <label for="fname"><span class="text-danger">* </span>First Name</label>
                <input type="text" name="fname" class="form-control" placeholder="Your First Name" required value="<?php echo set_value('fname') ?>">
            </div>
            <div class="col form-group">
                <label for="lname"><span class="text-danger">* </span>Last Name</label>
                <input type="text" name="lname" class="form-control" placeholder="Your Last Name" required value="<?php echo set_value('lname') ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="email"><span class="text-danger">* </span>Email</label>
            <input type="text" name="email" class="form-control" placeholder="example@domain.com" required value="<?php echo set_value('email') ?>">
        </div>
        <div class="form-group">
            <label for="email"><span class="text-danger">* </span>Gender</label>
            <select class="form-control" name="gender" required selected="<?php echo set_value('gender') ?>">
                <option value=""></option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="form-group mb-1">
            <label for="pwd"><span class="text-danger">* </span>Password</label>
            <input type="password" name="pwd" class="form-control pwd" placeholder="Password must be over 5characters" minlength="5" required>
        </div>
        <div class="text-right d-flex">
            <div class="col" style="margin:auto">
                <i class="pwd_eye fas fa-eye"></i>
                <i class="pwd_eye fas fa-eye-slash"></i>
            </div>
            <div>
                <button type="button" class="btn text-light genpwdbtn">Generate Password</button>
            </div>
        </div>
        <div class="form-group">
            <label for="p_image">Profile Image</label><br>
            <input type="file" name="p_image">
        </div>
        <div class="reg_div mt-5">
            <button type="submit" class="btn text-light btn-block">Create Account</button>
        </div>
        <div class="register_div text-center mt-3">
            <a href="<?php echo base_url('login'); ?>" class="font-weight-bolder" style="color:#333">LOGIN<i class="fas fa-chevron-circle-right ml-1"></i></a>
        </div>
    </form>
</div>

<script src="<?php echo base_url('assets/js/register.js') ?>"></script>