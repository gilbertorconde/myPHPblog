<?php

include('core/init.inc.php');
include('login/login.func.php');
sec_session_start();

echo '<!DOCTYPE HTML>

<html>'; 

include('resources/head.php');

echo 
'
<body>
<div id="main">
';
include('resources/github_ribbons.php');
$current = 'list';
include('resources/header.php');

echo
'
<div id="site_content">
';

include('resources/sidebar.php');

echo
'
<div id="content">';
$page = 0;
$hasnext = false;
if(isset($_GET['tag'])){
    $uri = "blog_list.php?tag=" . $_GET['tag'] ."&";
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    
    $posts = get_posts_by('tag', $_GET['tag'], $mysqli, $page);
    if ( count($posts) > MAX_ENTRY ){
        $hasnext = true;
        array_pop($posts);
    }
    
    echo '<h1>Pesquisa por "'.$_GET['tag'].'"</h1>';

} elseif(isset($_GET['user'])){
    $uri = "blog_list.php?user=" . $_GET['user'] ."&";
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    
    $posts = get_posts_by('user', $_GET['user'], $mysqli, $page);
    if ( count($posts) > MAX_ENTRY ){
        $hasnext = true;
        array_pop($posts);
    }
    echo '<h1>Pesquisa por "'.$_GET['user'].'"</h1>';

} else {
    $uri = "blog_list.php?";
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    $posts = get_posts(false, $mysqli, $page);
    if ( count($posts) > MAX_ENTRY ){
        $hasnext = true;
        array_pop($posts);
    }
    echo '<h1>Artigos</h1>';

}
$last_key = end(array_keys($posts));
foreach ($posts as $key => $post){
    echo "<article>\n";
    echo "<header>\n";
    echo "<h1><a href=\"blog_read.php?pid=".$post['id']."\">".html_entity_decode($post['title'])."</a></h1>\n";
    $dat = explode(' ', $post['date']);
    $hora = $dat[1];
    //$data = explode(' ', $post['date'])[0];
    $data = $dat[0];
    echo "<h5> Data: <time datetime=\"".$hora."\">".$data
        ."</time> por <a href=\"blog_list.php?user=".$post['user']."\">".$post['user']
        ."</a> - <a href=\"blog_read.php?pid=".$post['id']."#feedback\">".$post['total_comments']
        ." comentário(s) (ultimo ".$post['last_comment'].")</a></h5>\n";
    echo "</header>\n";
    $list_content = closetags($post['preview']);
    $list_content = strip_tags($list_content, '<img>');
    if( strlen($post['preview']) >= 1024 ){
        $list_content .= " [continua ...]";
    }
    echo "<div>".$list_content."<br /><p><a href=\"blog_read.php?pid=".$post['id']."\">Ver artigo completo</a></p></div>\n";
    $tags = get_tags($post['id'], $mysqli);
    if(isset($tags) && !(empty($tags))){
        echo "<p>Categorias: ";
        $last_key_tag = end(array_keys($tags));
        foreach($tags as $key_tag => $tag){
            echo "<a href=\"blog_list.php?tag=".urlencode($tag)."\">".$tag."</a>";
            if($key_tag != $last_key_tag){
                echo ", ";
            }
            else {
                echo "\n</p>";
            }
        }
    }
    if($key != $last_key){
    	echo "<br /><hr />\n";
    }
    echo "</article>\n";
}
if($hasnext){
    $nextp = $page + 1;
    echo "<div class=\"left\" ><a href=\"{$uri}page={$nextp}\">Mais Antigos</a></div>";
}
if(isset($_GET['page']) && $_GET['page'] > 0){
    $nextp = $_GET['page'] - 1;
    echo "<div class=\"right\" ><a href=\"{$uri}page={$nextp}\">Mais Recentes</a></div>";
}
echo
'</div>
</div>
';
include('resources/footer.php');
echo
'
</div>
</body>
</html>';

function closeTags( $html ) {
    preg_match_all("##iU", $html, $result, PREG_OFFSET_CAPTURE);

    if (!isset($result[1]))
        return $html;

    $openedtags = $result[1];
    $len_opened = count($openedtags);

    if (!$len_opened)
        return $html;

    preg_match_all("##iU", $html, $result, PREG_OFFSET_CAPTURE);
    $closedtags = array();

    foreach($result[1] as $tag)
        $closedtags[$tag[1]] = $tag[0];

    $openedtags = array_reverse($openedtags);

    for($i = 0; $i < $len_opened; $i++) {
        if (preg_match('/(img|br|hr)/i', $openedtags[$i][0]))
            continue;

        $found = array_search($openedtags[$i][0], $closedtags);

        if (!$found || $found < $openedtags[$i][1])
            $html .= "";

        if ($found)
            unset($closedtags[$found]);
    }

    return $html;
}

?>
