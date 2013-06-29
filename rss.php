<?php

include('core/init.inc.php');

header("Content-Type: application/rss+xml; charset=UTF-8");
 
DEFINE ('DB_USER', 'my_username');  
DEFINE ('DB_PASSWORD', 'my_password');  
DEFINE ('DB_HOST', 'localhost');  
DEFINE ('DB_NAME', 'my_database');
 
$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>'. PHP_EOL;
$rssfeed .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'. PHP_EOL;
$rssfeed .= '<channel>'. PHP_EOL;
$rssfeed .= '<atom:link href="http://myphpblog.vacau.com/rss.php" rel="self" type="application/rss+xml" />' . PHP_EOL;
$rssfeed .= '<title>MyPHPBlog</title>'. PHP_EOL;
$rssfeed .= '<link>' . htmlentities('http://myphpblog.vacau.com') . '</link>'. PHP_EOL;
$rssfeed .= '<description>Blog dedicado ao desenvolvimento de um blog em php.</description>'. PHP_EOL;
$rssfeed .= '<language>pt-pt</language>'. PHP_EOL;
$rssfeed .= '<copyright>Copyleft :) 2013 Gilberto Conde</copyright>'. PHP_EOL;

$result = get_posts(true);
$users = get_users();
foreach($result as $row) {
    extract($row);
    $email = "";
    $pid = $id;
    if(isset($user)){
        foreach($users as $use) {
            extract($use);
            if($user_name === $user){
                $email = $user_email;
                break;
            }
        }
    }
    $rssfeed .= '<item>'. PHP_EOL;
    $rssfeed .= '<title>' . html_entity_decode($title) . '</title>' . PHP_EOL;
    $rssfeed .= '<description><![CDATA[' . $preview . ']]></description>' . PHP_EOL;
    $rssfeed .= '<author>' . $email . ' (' . $user . ')' . '</author>' . PHP_EOL;
    $rssfeed .= '<link>' . htmlentities('http://myphpblog.vacau.com/blog_read.php?pid=' . $pid) . '</link>' . PHP_EOL;
    $rssfeed .= '<guid>' . htmlentities('http://myphpblog.vacau.com/blog_read.php?pid=' . $pid) . '</guid>' . PHP_EOL;
    $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($date)) . '</pubDate>' . PHP_EOL;
    
    $tags = get_tags($pid);
    if(isset($tags) && !(empty($tags))){
        foreach($tags as $key_tag => $tag){
            $rssfeed .= '<category><![CDATA[' . $tag . ']]></category>' . PHP_EOL;
        }
    }
    $rssfeed .= '</item>' . PHP_EOL;
}
 
$rssfeed .= '</channel>' . PHP_EOL;
$rssfeed .= '</rss>' . PHP_EOL;
 
echo $rssfeed;
?>