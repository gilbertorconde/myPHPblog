<?php

// retorna todos os comentarios do post passado por pid
function get_comments($pid, $mysqli){
    $pid = (int)$pid;
    $return = array();
    $sql = "SELECT
               `comment_body` AS `body`,
               `comment_user` AS `user`,
               `comment_id`,
               DATE_FORMAT(`comment_date`, '%d/%m/%Y %H:%i:%s') AS `date`
            FROM `comments`
            WHERE `post_id` = {$pid}";
    
    
    if($stmt = $mysqli->query($sql)){
        while ($row = $stmt->fetch_assoc()){
            $return[] = $row;
        }
    }
    return $return;
}

// adiciona um comentario ao post pid
function add_comment($pid, $user, $body, $mysqli){
    if(valid_pid($pid, $mysqli) === false){
        return false;
    }
    $pid = (int)$pid;
    $user = $mysqli->real_escape_string(htmlentities($user));
    $body = $mysqli->real_escape_string(nl2br(htmlentities($body)));
    
    $mysqli->query("INSERT INTO `comments` (`post_id`, `comment_user`, `comment_body`, `comment_date`) VALUES({$pid},'{$user}', '{$body}', NOW())");
    return true;
}

function remove_comments( $pid, $mysqli ){
    $pid = (int)$pid;
    $mysqli->query("DELETE FROM `comments` WHERE `comments`.`post_id` = {$pid}");
}
function remove_comment( $cid, $mysqli ){
    $cid = (int)$cid;
    $mysqli->query("DELETE FROM `comments` WHERE `comments`.`comment_id` = {$cid}");
}

?>