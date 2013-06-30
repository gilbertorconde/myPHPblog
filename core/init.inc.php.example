<?php

$host = '';
$usernm = '';
$passwd = '';
$dbname = '';
$link = '';

$mysqli = new mysqli($host, $usernm, $passwd, $dbname);

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

include('inc/posts.inc.php');
include('inc/comments.inc.php');

?>