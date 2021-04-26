
function opennav() {
    document.getElementById('side-nav').style.width = "160px";
    $('body').css('margin-left', '110px').fadeIn();
    $('div ul li a.nav-link span,.userfullname').fadeIn();
    $('.closex').attr('onclick', 'closenav()').css({ 'right': '13px', 'padding': '0' });
}

function closenav() {
    document.getElementById('side-nav').style.width = "50px";
    $('body').css('margin-left', '0');
    $('div ul li a.nav-link span,.userfullname').hide();
    $('.closex').attr('onclick', 'opennav()');
}

$('[data-toggle="tooltip"]').tooltip();

setTimeout(() => document.querySelector('.alert').remove(), 6000);

$(document).ready(function () {

    $(document).on("click", ".ajax_succ_div_close", function () {
        $(".ajax_succ_div").fadeOut();
    });

    $(document).on("click", ".ajax_err_div_close", function () {
        $(".ajax_err_div").fadeOut();
    });
})