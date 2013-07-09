<?php

include('core/init.inc.php');

//header("Content-Type: application/rss+xml; charset=UTF-8");
header("Content-Type: text/xml; charset=UTF-8");
 
$rssfeed = '<?xml-stylesheet type="text/xsl"  href="http://myphpblog.vacau.com/style/rss.xsl"?>'. PHP_EOL;
$rssfeed .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'. PHP_EOL;
$rssfeed .= '	<channel>'. PHP_EOL;
$rssfeed .= '		<atom:link href="http://myphpblog.vacau.com/rss.php" rel="self" type="application/rss+xml" />' . PHP_EOL;
$rssfeed .= '		<title>MyPHPBlog</title>'. PHP_EOL;
$rssfeed .= '		<link>' . htmlentities('http://myphpblog.vacau.com') . '</link>'. PHP_EOL;
$rssfeed .= '		<description>Blog dedicado ao desenvolvimento de um blog em php.</description>'. PHP_EOL;
$rssfeed .= '		<language>pt-pt</language>'. PHP_EOL;
$rssfeed .= '		<copyright>Copyleft :) 2013 Gilberto Conde</copyright>'. PHP_EOL;

$result = get_posts(true, $mysqli);
$users = get_users($mysqli);
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
    $rssfeed .= '		<item>'. PHP_EOL;
    $rssfeed .= '			<title>' . html_entity_decode($title) . '</title>' . PHP_EOL;
    $rssfeed .= '			<description><![CDATA[' . strip_tags($preview, '<img><a>') . ']]></description>' . PHP_EOL;
    $rssfeed .= '			<author>' . $email . ' (' . $user . ')' . '</author>' . PHP_EOL;
    $rssfeed .= '			<link>' . htmlentities('http://myphpblog.vacau.com/blog_read.php?pid=' . $pid) . '</link>' . PHP_EOL;
    $rssfeed .= '			<guid>' . htmlentities('http://myphpblog.vacau.com/blog_read.php?pid=' . $pid) . '</guid>' . PHP_EOL;
    $rssfeed .= '			<pubDate>' . date("D, d M Y H:i:s O", strtotime($date)) . '</pubDate>' . PHP_EOL;
    
    $tags = get_tags($pid, $mysqli);
    if(isset($tags) && !(empty($tags))){
        foreach($tags as $key_tag => $tag){
            $rssfeed .= '			<category><![CDATA[' . $tag . ']]></category>' . PHP_EOL;
        }
    }
    $rssfeed .= '		</item>' . PHP_EOL;
}
 
$rssfeed .= '	</channel>' . PHP_EOL;
$rssfeed .= '</rss>' . PHP_EOL;
 
echo $rssfeed;
?>