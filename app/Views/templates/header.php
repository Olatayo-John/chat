<!DOCTYPE html>
<html lang="en">
<?php
$this->session = session();
$this->uri = service('uri');
$this->validator = service('validation');
?>

<head>
    <title><?php echo (!empty($title) ? esc(ucfirst($title)) : ucfirst(env('APP'))) ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/ca92620e44.js" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url('assets/js/header.js'); ?>"></script>
    <link href="<?php echo base_url('assets/css/header.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?php echo base_url('assets/images/logo_dark.png') ?>">
    <!-- <script type="text/javascript">
        document.onreadystatechange = function() {
            if (document.readyState !== "complete") {
                $(".spinnerdiv").show();
            } else {
                $(".spinnerdiv").fadeOut();
            }
        };
    </script> -->
</head>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="padding: 0 20px;position:sticky !important;margin-left: 50px;">

    <div class="navbar-brand">
        <img src="<?php echo base_url('assets/images/logo_dark.png') ?>" alt="" width="50px" height="50px">
    </div>
    <label class="navbar-brand">
        ChatApp
    </label>
    <?php if ($this->session->get('logged_in') == '1') : ?>
        <a href="<?php echo base_url('profile') ?>" class="ml-auto" style="color:#333" data-toggle="tooltip" title="<?php echo ucfirst($this->session->get('fname')) . ' ' . ucfirst($this->session->get('lname')) ?>">
            <img src="<?php echo base_url('assets/images/') . $this->session->get('p_image'); ?>" width="50px" height="50px" style="border-radius: 50%">
        </a>
    <?php endif; ?>

    <div class="side-nav" id="side-nav">
        <a href="javascript:void(0)" class="closex" onclick="opennav()">&#9776;</a>
        <div class="userfullname pb-4">
            <span><?php echo ucfirst($this->session->get('fname')) . ' ' . ucfirst($this->session->get('lname')) ?></span>
        </div>
        <?php $url = $this->uri->getSegment(1); ?>
        <ul <?php echo $url ?>>
            <?php if (!$this->session->get('logged_in')) : ?>
                <li class="nav-item"><a href="<?php echo base_url('login') ?>" class="nav-link" style="<?php echo ($url == 'login' || $url == '') ? 'background:white;color:black;' : '' ?>"><i class="fas fa-user" style="<?php echo ($url == 'login' || $url == '') ? 'background:white;color:black' : '' ?>"></i><span>Login</span></a>
                </li>
                <li class="nav-item"><a href="<?php echo base_url('register') ?>" class="nav-link" style="<?php echo ($url == 'register') ? 'background:white;color:black;' : '' ?>"><i class="fas fa-user-plus" style="<?php echo ($url == 'register') ? 'background:white;color:black' : '' ?>"></i><span>Register</span></a>
                </li>
            <?php endif; ?>
            <?php if ($this->session->get('logged_in') == '1') : ?>
                <li class="nav-item"><a href="<?php echo base_url('chats') ?>" class="nav-link" style="<?php echo ($url == 'chats') ? 'background:white;color:black;' : '' ?>"><i class="fas fa-comments" style="<?php echo ($url == 'chats') ? 'background:white;color:black' : '' ?>"></i><span>Chats</span></a>
                </li>
                <li class="nav-item"><a href="<?php echo base_url('friends') ?>" class="nav-link" style="<?php echo ($url == 'friends') ? 'background:white;color:black;' : '' ?>"><i class="fas fa-users" style="<?php echo ($url == 'friends') ? 'background:white;color:black' : '' ?>"></i><span>Friends</span></a>
                </li>
                <li class="nav-item"><a href="<?php echo base_url('profile') ?>" class="nav-link" style="<?php echo ($url == 'profile' || $url == 'edit') ? 'background:white;color:black;' : '' ?>"><i class="fas fa-user-circle" style="<?php echo ($url == 'profile' || $url == 'edit') ? 'background:white;color:black' : '' ?>"></i><span>Profile</span></a>
                </li>
                <li class="nav-item"><a href="<?php echo base_url('settings') ?>" class="nav-link" style="<?php echo ($url == 'settings' || $url == 'settings') ? 'background:white;color:black;' : '' ?>"><i class="fas fa-cog" style="<?php echo ($url == 'settings' || $url == 'settings') ? 'background:white;color:black' : '' ?>"></i><span>Settings</span></a>
                </li>
            <?php endif; ?>
            <li class="nav-item"><a href="<?php echo base_url('contact') ?>" class="nav-link" style="<?php echo ($url == 'contact') ? 'background:white;color:black;' : '' ?>"><i class="fas fa-id-card" style="<?php echo ($url == 'contact') ? 'background:white;color:black;' : '' ?>"></i><span>Contact Us</span></a>
            </li>
            <?php if ($this->session->get('logged_in')) : ?>
                <li class="nav-item"><a href="<?php echo base_url('logout') ?>" class="nav-link text-danger">
                        <i class="fas fa-sign-out-alt text-danger"></i><span>Logout</span></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<body>
    <div class="container">
        <?php if ($this->session->getFlashdata('invalid')) : ?>
            <div class="alert invalid">
                <strong><i class="fas fa-exclamation-circle mr-2"></i><?php echo $this->session->getFlashdata('invalid'); ?></strong>
            </div>
        <?php endif; ?>
        <?php if ($this->session->getFlashdata('valid')) : ?>
            <div class="alert valid">
                <strong><i class="fas fa-bell mr-2"></i><?php echo $this->session->getFlashdata('valid'); ?></strong>
            </div>
        <?php endif; ?>

        <!-- form error -->
        <?php $errors = $this->validator->getErrors();  ?>
        <?php if (count($errors) > 0) : ?>
            <div class="alert invalid">
                <?php foreach ($errors as $error) : ?>
                    <strong><i class="fas fa-exclamation-circle mr-2"></i><?php echo $error ?></strong><br>
                <?php endforeach ?>
            </div>
        <?php endif; ?>

        <!-- ajax failed -->
        <div class="ajax_alert_div ajax_err_div mt-2" style="padding:8px;display:none;z-index: 9999;">
            <i class="fas fa-exclamation-circle"></i>
            <strong class="ajax_res_err"></strong>
        </div>
        <!-- ajax-success -->
        <div class="ajax_alert_div ajax_succ_div mt-2" style="padding:8px;display:none;z-index: 9999;">
            <i class="fas fa-bell"></i>
            <strong class="ajax_res_succ"></strong>
        </div>
    </div>