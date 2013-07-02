<?php

DEFINE("MAX_ENTRY", 5);

// Verifica se o post passado existe
function valid_pid($pid, $mysqli){
    $pid = (int)$pid;
    
    if($stmt = $mysqli->prepare("SELECT COUNT(`post_id`) as `total` from `posts` WHERE `post_id` = {$pid}")){
        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        if ($total != 1){
            return false;
        }
        else {
            return true;
        }
    }
    return false;
}

// retorna todos os posts do blog
function get_posts($isFull = false, $mysqli = false, $page = null){
    $limits = "";
    if($page !== null){
        $pos = $page * MAX_ENTRY;
        $quant = MAX_ENTRY + 1;
        $limits = "LIMIT {$pos} , {$quant}";
    }
    $dim = $isFull ? '`posts`.`post_body`' :'LEFT( `posts`.`post_body` , 1024 )';
    $dat = $isFull ? '`posts`.`post_date`' :"DATE_FORMAT( `posts`.`post_date` , '%d/%m/%Y %H:%i:%s' )";
    $sql = "SELECT `posts`.`post_id` AS `id` , `posts`.`post_title` AS `title` , ".$dim." AS `preview` , `posts`.`post_user` AS `user` , " . $dat . " AS `date` , `comments`.`total_comments` AS `total_comments` , DATE_FORMAT( `comments`.`last_comment` , '%d/%m/%Y %H:%i:%s' ) AS `last_comment`
FROM `posts`
LEFT JOIN (

SELECT `post_id` , COUNT( `comment_id` ) AS `total_comments` , MAX( `comment_date` ) AS `last_comment`
FROM `comments`
GROUP BY `post_id`
) AS `comments` ON `posts`.`post_id` = `comments`.`post_id`
ORDER BY `posts`.`post_date` DESC
{$limits}
";
    
    $rows = array();
    
    if($mysqli != false){
        if($stmt = $mysqli->query($sql)){
            while ($row = $stmt->fetch_assoc()){
                $row['total_comments'] = ($row['total_comments'] === null) ? 0 : $row['total_comments'];
                $row['last_comment']  = ($row['last_comment'] === null) ? 'nunca' : $row['last_comment'];
                $rows[] = $row;
            }
        }
    }
    return $rows;
}


function get_posts_by($type, $value, $mysqli, $page = null){
    
    $limits = "";
    if($page !== null){
        $pos = $page * MAX_ENTRY;
        $quant = MAX_ENTRY + 1;
        $limits = "LIMIT {$pos} , {$quant}";
    }
    
    $value = $mysqli->real_escape_string($value);
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
ORDER BY `posts`.`post_date` DESC
{$limits}
";
    } elseif($type == 'tag') {
        $sql = "SELECT `posts`.`post_id` AS `id` , `posts`.`post_title` AS `title` , LEFT( `posts`.`post_body` , 512 ) AS `preview` , `posts`.`post_user` AS `user` , DATE_FORMAT( `posts`.`post_date` , '%d/%m/%Y %H:%i:%s' ) AS `date` , `comments`.`total_comments` AS `total_comments` , DATE_FORMAT( `comments`.`last_comment` , '%d/%m/%Y %H:%i:%s' ) AS `last_comment`
FROM `posts`
LEFT JOIN (
SELECT `post_id` , COUNT( `comment_id` ) AS `total_comments` , MAX( `comment_date` ) AS `last_comment`
FROM `comments`
GROUP BY `post_id`
) AS `comments` ON `posts`.`post_id` = `comments`.`post_id`
ORDER BY `posts`.`post_date` DESC
{$limits}
";
        
        $pids = array();
        if($stmt = $mysqli->query("SELECT `tags`.`post_id` as `id` FROM `tags` WHERE `tag_name`='{$value}'")){
            while ($row = $stmt->fetch_assoc()){
                $pids[] = $row['id'];
            }
        }
    }
    
    $rows = array();
    
    if($mysqli != false){
        if($stmt = $mysqli->query($sql)){
            while ($row = $stmt->fetch_assoc()){
                if (($type == 'tag' && in_array($row['id'], $pids)) || $type == 'user'){
                    $row['total_comments'] = ($row['total_comments'] === null) ? 0 : $row['total_comments'];
                    $row['last_comment']  = ($row['last_comment'] === null) ? 'nunca' : $row['last_comment'];
                    $rows[] = $row;
                }
            }
        }
    }
    return $rows;
}


function get_users($mysqli){
    
    $rows = array();
    if($stmt = $mysqli->query("SELECT `id` as `user_id`, `username` as `user_name`, `email` as `user_email` FROM `members`")){
        while ($row = $stmt->fetch_assoc()){
            $rows[] = $row;
        }
    }
    return $rows;
}

function get_user($uid, $mysqli){
    $uid = (int)$uid;
    if($stmt = $mysqli->query("SELECT `username`, `email`, `about` FROM `members` WHERE `id`={$uid}")){
        $row = $stmt->fetch_assoc();
    }
    return $row;
}

// retorna o post com o id pid

function get_post( $pid, $mysqli ){
    $pid = (int)$pid;
    $row = array();
    $sql = "SELECT
               `post_title` AS `title`,
               `post_body`  AS `body`,
               `post_user`  AS `user`,
                DATE_FORMAT(`post_date`, '%d/%m/%Y %H:%i:%s' )  AS `date`
            FROM `posts`
            WHERE `post_id` = {$pid}";
    
    if($stmt = $mysqli->query($sql)){
        $row = $stmt->fetch_assoc();
        $sql = "SELECT `about` FROM `members` WHERE `username` = '".$row['user']."' LIMIT 1";
        if($stmt = $mysqli->query($sql)){
            $about = $stmt->fetch_assoc();
            $row['about_user'] = $about['about'];
        }
    }
    $row['comments'] = get_comments($pid, $mysqli);
    return $row;
}

function get_tags( $pid, $mysqli ){
    $pid = (int)$pid;
    $sql = "SELECT
               `tag_name`
            FROM `tags`
            WHERE `post_id` = {$pid}";
    
    $rows = array();
    if($stmt = $mysqli->query($sql)){
        while ($row = $stmt->fetch_assoc()){
            $rows[] = $row['tag_name'];
        }
    }
    return $rows;
}

function get_all_tags($mysqli){
    $sql = "SELECT DISTINCT `tag_name`
            FROM `tags`
            ORDER BY `tags`.`tag_name` ASC";
    
    $rows = array();
    if($stmt = $mysqli->query($sql)){
        while ($row = $stmt->fetch_assoc()){
            $rows[] = $row['tag_name'];
        }
    }
    return $rows;
}

function remove_post( $pid, $mysqli ){
    $pid = (int)$pid;
    remove_comments( $pid, $mysqli );
    $mysqli->query("DELETE FROM `posts` WHERE `posts`.`post_id` = $pid");
    $mysqli->query("DELETE FROM `tags` WHERE `tags`.`post_id` = $pid");
}

function change_about($user_id, $about, $mysqli){
	//$about = $mysqli->real_escape_string(nl2br(htmlentities($about)));
    $mysqli->query("UPDATE `members` SET `about` = '$about' WHERE `id` = $user_id");
}

// adiciona um novo post a base de dados
function add_post($name, $title, $body, $tags, $mysqli){
    $name = $mysqli->real_escape_string(htmlentities($name));
    $title = $mysqli->real_escape_string(htmlentities($title));
    //$body = $mysqli->real_escape_string(nl2br(htmlentities($body)));
    $mysqli->query("INSERT INTO `posts` (`post_user`, `post_title`,`post_body`, `post_date`) VALUE ('{$name}', '{$title}', '{$body}', NOW())");
    $pid = $mysqli->insert_id;
    foreach($tags as $tag){
        $mysqli->query("INSERT INTO `tags` (`post_id`, `tag_name`) VALUE ('{$pid}', '{$tag}')");
    }
    return $pid;
    
}

function change_title_post($title, $pid, $mysqli){
    $pid = (int)$pid;
    $title = $mysqli->real_escape_string(htmlentities($title));
    $mysqli->query("UPDATE `posts` SET `post_title` = '".$title."',
                `post_date` = NOW( ) WHERE `post_id` =".$pid."");
}

function change_body_post($body, $pid, $mysqli){
    $pid = (int)$pid;
    $mysqli->query("UPDATE `posts` SET `post_body` = '".$body."',
                `post_date` = NOW( ) WHERE `post_id` =".$pid."");
}
?>
