<?php

$host = 'localhost';
$usernm = 'root';
$passwd = '@rcarlos';
$dbname = 'phpBlog';
$link = '';

$mysql_host = "mysql15.000webhost.com";
$mysql_database = "a5877085_phpBlog";
$mysql_user = "a5877085_user";
$mysql_password = "2Rcarlos";

mysql_connect($host, $usernm, $passwd);
mysql_select_db($dbname);

$mysqli = new mysqli($host, $usernm, $passwd, $dbname);

//mysql_connect($mysql_host, $mysql_user, $mysql_password);
//mysql_select_db($mysql_database);

//$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);

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