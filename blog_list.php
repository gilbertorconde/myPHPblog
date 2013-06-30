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
if(isset($_GET['tag'])){

    $posts = get_posts_by('tag', $_GET['tag']);
    echo '<h1>Pesquisa por "'.$_GET['tag'].'"</h1>';

} elseif(isset($_GET['user'])){

    $posts = get_posts_by('user', $_GET['user']);
    echo '<h1>Pesquisa por "'.$_GET['user'].'"</h1>';

} else {

    $posts = get_posts();
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
    echo "<div>".closetags($post['preview'])."<br /><p><a href=\"blog_read.php?pid=".$post['id']."\">Ver artigo completo</a></p></div>\n";
    $tags = get_tags($post['id']);
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

function closetags ( $html )
{
    //put all opened tags into an array
    preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
    $openedtags = $result[1];
    //put all closed tags into an array
    preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
    $closedtags = $result[1];
    $len_opened = count ( $openedtags );
    //all tags are closed
    if( count ( $closedtags ) == $len_opened )
        {
            return $html;
        }
    $openedtags = array_reverse ( $openedtags );
    //close tags
    for( $i = 0; $i < $len_opened; $i++ )
        {
            if ( !in_array ( $openedtags[$i], $closedtags ) )
                {
                    $html .= "</" . $openedtags[$i] . ">";
                }
            else
                {
                    unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
                }
        }
    return $html;
}

?>
