<link href="<?php echo base_url('assets/css/emailverify.css'); ?>" rel="stylesheet">
<div class="content mr-3 mb-5 mt-5">
    <form method="post" action="<?= base_url("emailverify/" . $key) ?>" class="container col-md-5">
        <input type="hidden" class="csrf_token" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">
        <div class="form-group">
            <div class="pb-5 pt-5 text-center font-weight-bold">Enter verification code sent to your email</div>
            <div class="d-flex" style="border-bottom: 1px solid #333">
                <i class="fas fa-key" style="margin:auto 0"></i>
                <input type="number" name="vcode" class="form-control" placeholder="Verification Code" required value="<?php echo set_value('vcode') ?>">
            </div>
        </div>
        <div>
            <button type="submit" class="btn text-light btn-block">Verify</button>
        </div>
        <div class="resend text-right mt-3">
            <a href="<?= base_url("resend_vcode/" . $key) ?>">Resend code?</a>
        </div>
    </form>