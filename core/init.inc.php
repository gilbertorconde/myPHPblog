<?php

$host = '';
$usernm = '';
$passwd = '';
$dbname = '';
$link = '';

$mysql_host = "";
$mysql_database = "";
$mysql_user = "";
$mysql_password = "";

//mysql_connect($host, $usernm, $passwd);
//mysql_select_db($dbname);

mysql_connect($mysql_host, $mysql_user, $mysql_password);
mysql_select_db($mysql_database);

//$mysqli = new mysqli($host, $usernm, $passwd, $dbname);
$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);

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