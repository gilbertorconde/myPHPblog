<?php

include('../core/init.inc.php');
include('../login/login.func.php');
sec_session_start();
if(login_check($mysqli) !== true) {
    echo "false";
    die();
} 
if(isset($_POST['title'], $_POST['pid'])){
    change_title_post($_POST['title'], $_POST['pid'], $mysqli);
    echo "Feito tt";
}

if(isset($_POST['body'], $_POST['pid'])){
    change_body_post($_POST['body'], $_POST['pid'], $mysqli);
    echo "Feito bd";
}
if(isset($_POST['cid'])){
    remove_comment( $_POST['cid'], $mysqli );
    echo "Feito rc";
}
?>
