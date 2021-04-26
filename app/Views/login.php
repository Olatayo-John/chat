<link href="<?php echo base_url('assets/css/login.css'); ?>" rel="stylesheet">

<div class="content mr-3 mb-3 mt-5">
    <form method="post" action="login" class="container col-md-4">
        <input type="hidden" class="csrf_token" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">
        <h4>Login</h4>
        <div class="form-group pb-3 pt-5">
            <label for="email"><span class="text-danger">* </span>Email</label>
            <input type="email" name="current-email" style="opacity:0">
            <div class="d-flex" style="border-bottom: 1px solid #333">
                <i class="fas fa-at"></i>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required autofocus value="<?= set_value("email") ?>">
            </div>
            <span class="text-danger font-weight-bolder m-0"></span>
        </div>
        <div class="form-group">
            <label for="pwd"><span class="text-danger">* </span>Password</label>
            <input type="password" style="opacity:0">
            <div class="d-flex" style="border-bottom: 1px solid #333">
                <i class="fas fa-lock"></i>
                <input type="password" name="pwd" class="form-control" placeholder="Enter your password" required>
            </div>
            <span class="text-danger font-weight-bolder m-0"></span>
        </div>
        <div class="text-right mt-2">
            <a href="<?php echo base_url(); ?>" class="font-weight-bolder" style="color:#721c24">Forgot your password?</a>
        </div>
        <div class="loginbtn mt-5">
            <button type="submit" class="btn text-light btn-block">LOGIN</button>
        </div>

        <div class="registerlink text-center mt-5">
            <a href="<?php echo base_url('register'); ?>" class="" style="color:#333">SIGN UP<i class="fas fa-chevron-circle-right ml-1"></i></a>
        </div>
    </form>
</div>