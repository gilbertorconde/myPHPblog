<?php

// Verifica se o post passado existe
function valid_pid($pid){
    $pid = (int)$pid;

    $total = mysql_query("SELECT COUNT(`post_id`) from `posts` WHERE `post_id` = {$pid}");
    $total = mysql_result($total, 0);
    
    if ($total != 1){
        return false;
    }
    else {
        return true;
    }
}

// retorna todos os posts do blog
function get_posts($isFull = false){
    $dim = $isFull ? '`posts`.`post_body`' :'LEFT( `posts`.`post_body` , 512 )';
    $dat = $isFull ? '`posts`.`post_date`' :"DATE_FORMAT( `posts`.`post_date` , '%d/%m/%Y %H:%i:%s' )";
    $sql = "SELECT `posts`.`post_id` AS `id` , `posts`.`post_title` AS `title` , ".$dim." AS `preview` , `posts`.`post_user` AS `user` , " . $dat . " AS `date` , `comments`.`total_comments` AS `total_comments` , DATE_FORMAT( `comments`.`last_comment` , '%d/%m/%Y %H:%i:%s' ) AS `last_comment`
FROM `posts`
LEFT JOIN (

SELECT `post_id` , COUNT( `comment_id` ) AS `total_comments` , MAX( `comment_date` ) AS `last_comment`
FROM `comments`
GROUP BY `post_id`
) AS `comments` ON `posts`.`post_id` = `comments`.`post_id`
ORDER BY `posts`.`post_date` DESC";

    $post = mysql_query($sql);

    $rows = array();
    while (($row = mysql_fetch_assoc($post)) !== false){
        $rows[] = array(
                        'id'             => $row['id'],
                        'title'          => $row['title'],
                        'preview'        => $row['preview'],
                        'user'           => $row['user'],
                        'date'           => $row['date'],
                        'total_comments' => ($row['total_comments'] === null) ? 0 : $row['total_comments'],
                        'last_comment'  => ($row['last_comment'] === null) ? 'nunca' : $row['last_comment']
                        );
    }
    return $rows;
}


function get_posts_by($type, $value){
    $value = mysql_real_escape_string($value);
    $sql = "";
    $pids = array();
    if ($type == 'user'){
        $sql = "SELECT `posts`.`post_id` AS `id` , `posts`.`post_title` AS `title` , LEFT( `posts`.`post_body` , 512 ) AS `preview` , `posts`.`post_user` AS `user` , DATE_FORMAT( `posts`.`post_date` , '%d/%m/%Y %H:%i:%s' ) AS `date` , `comments`.`total_comments` AS `total_comments` , DATE_FORMAT( `comments`.`last_comment` , '%d/%m/%Y %H:%i:%s' ) AS `last_comment`
FROM `posts`
LEFT JOIN (
SELECT `post_id` , COUNT( `comment_id` ) AS `total_comments` , MAX( `comment_date` ) AS `last_comment`
FROM `comments`
GROUP BY `post_id`
) AS `comments` ON `posts`.`post_id` = `comments`.`post_id`
WHERE `posts`.`post_user` = '$value'
ORDER BY `posts`.`post_date` DESC";
    } elseif($type == 'tag') {
        $sql = "SELECT `posts`.`post_id` AS `id` , `posts`.`post_title` AS `title` , LEFT( `posts`.`post_body` , 512 ) AS `preview` , `posts`.`post_user` AS `user` , DATE_FORMAT( `posts`.`post_date` , '%d/%m/%Y %H:%i:%s' ) AS `date` , `comments`.`total_comments` AS `total_comments` , DATE_FORMAT( `comments`.`last_comment` , '%d/%m/%Y %H:%i:%s' ) AS `last_comment`
FROM `posts`
LEFT JOIN (
SELECT `post_id` , COUNT( `comment_id` ) AS `total_comments` , MAX( `comment_date` ) AS `last_comment`
FROM `comments`
GROUP BY `post_id`
) AS `comments` ON `posts`.`post_id` = `comments`.`post_id`
ORDER BY `posts`.`post_date` DESC";
    
        $sqli = "SELECT `tags`.`post_id` as `id` FROM `tags` WHERE `tag_name`='{$value}'";
        $vals = mysql_query($sqli);
        while (($row = mysql_fetch_assoc($vals)) !== false){
            $pids[] = $row['id'];
        }
    }
    $post = mysql_query($sql);
    $rows = array();
    while (($row = mysql_fetch_assoc($post)) !== false){
        if (($type == 'tag' && in_array($row['id'], $pids)) || $type == 'user'){
            $rows[] = array(
                            'id'             => $row['id'],
                            'title'          => $row['title'],
                            'preview'        => $row['preview'],
                            'user'           => $row['user'],
                            'date'           => $row['date'],
                            'total_comments' => ($row['total_comments'] === null) ? 0 : $row['total_comments'],
                            'last_comment'  => ($row['last_comment'] === null) ? 'nunca' : $row['last_comment']
                            );
        }
    }
    return $rows;
}


function get_users(){
    $sql = "SELECT id, username, email FROM `members`";
    $post = mysql_query($sql);
    $rows = array();
    while (($row = mysql_fetch_assoc($post)) !== false){
        $rows[] = array(
                        'user_id'             => $row['id'],
                        'user_email'        => $row['email'],
                        'user_name'           => $row['username']
                        );
    }
    return $rows;
}

function get_user($uid){
    $uid = (int)$uid;
    $sql = "SELECT `username`, `email`, `about` FROM `members` WHERE `id`={$uid}";
    $result = mysql_query($sql);
    $rows = array();
    $row = mysql_fetch_assoc($result);
    return $row;
}

// retorna o post com o id pid
function get_post( $pid ){
    $pid = (int)$pid;
    $sql = "SELECT
               `post_title` AS `title`,
               `post_body`  AS `body`,
               `post_user`  AS `user`,
                DATE_FORMAT(`post_date`, '%d/%m/%Y %H:%i:%s' )  AS `date`
            FROM `posts`
            WHERE `post_id` = {$pid}";
    
    $post = mysql_query($sql);
    $post = mysql_fetch_assoc($post);
    
    $post['comments'] = get_comments($pid);
    $sql = "SELECT `about` FROM `members` WHERE `username` = '".$post['user']."' LIMIT 1";
    $aboutc = mysql_query($sql);
    $row = mysql_fetch_assoc($aboutc);
    $post['about_user'] = $row['about'];

    return $post;
}

function get_tags( $pid ){
    $pid = (int)$pid;
    $sql = "SELECT
               `tag_name`
            FROM `tags`
            WHERE `post_id` = {$pid}";
    $result = mysql_query($sql);
    $rows = array();
    while($row = mysql_fetch_assoc($result)){
        $rows[] = $row['tag_name'];
    }
    return $rows;
}

function get_all_tags(){
    $sql = "SELECT DISTINCT `tag_name`
            FROM `tags`
            ORDER BY `tags`.`tag_name` ASC";
    $result = mysql_query($sql);
    $rows = array();
    while($row = mysql_fetch_assoc($result)){
        $rows[] = $row['tag_name'];
    }
    return $rows;
}

function remove_post( $pid ){
    $pid = (int)$pid;
    mysql_query("DELETE FROM `comments` WHERE `comments`.`post_id` = $pid");
    mysql_query("DELETE FROM `posts` WHERE `posts`.`post_id` = $pid");
    mysql_query("DELETE FROM `tags` WHERE `tags`.`post_id` = $pid");
}

function change_about($user_id, $about){
	//$about = mysql_real_escape_string(nl2br(htmlentities($about)));
	mysql_query("UPDATE `members` SET `about` = '$about' WHERE `id` = $user_id;");
}

// adiciona um novo post a base de dados
function add_post($name, $title, $body, $tags){
    $name = mysql_real_escape_string(htmlentities($name));
    $title = mysql_real_escape_string(htmlentities($title));
    //$body = mysql_real_escape_string(nl2br(htmlentities($body)));
    mysql_query("INSERT INTO `posts` (`post_user`, `post_title`,`post_body`, `post_date`) VALUE ('{$name}', '{$title}', '{$body}', NOW())");
    $pid = mysql_insert_id();
    foreach($tags as $tag){
        mysql_query("INSERT INTO `tags` (`post_id`, `tag_name`) VALUE ('{$pid}', '{$tag}')");
    }
    return $pid;
    
}

function change_title_post($title, $pid){
    $pid = (int)$pid;
    $title = mysql_real_escape_string(htmlentities($title));
    mysql_query("UPDATE `posts` SET `post_title` = '".$title."',
                `post_date` = NOW( ) WHERE `post_id` =".$pid."");
}

function change_body_post($body, $pid){
    $pid = (int)$pid;
    mysql_query("UPDATE `posts` SET `post_body` = '".$body."',
                `post_date` = NOW( ) WHERE `post_id` =".$pid."");
}
?>
