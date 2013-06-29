<?php

if (isset($_SESSION['username'])){
    $fc = '<li><a href="blog_edit_user.php">Bem vindo '.$_SESSION['username'].'</a></li>
<li><a href="login/process.logout.php">Sair</a></li>'."\n";
} else {
    $fc = '<li><a href="login/login.php">Entrar</a></li>'."\n";
}

if (isset($current) && !(isset($_GET['tag']) || isset($_GET['user']))){
	if($current == "list"){
        echo "\n".'<li><a href="blog_post.php">Publicar</a></li>'."\n";
		echo '<li class="current"><a href="blog_list.php">Inicio</a></li>'."\n";
        echo $fc;
	}
	if($current == "post"){
        echo "\n".'<li class="current"><a href="blog_post.php">Publicar</a></li>'."\n";
		echo '<li><a href="blog_list.php">Inicio</a></li>'."\n";
        echo $fc;
	}
    if($current == "user"){
        echo "\n".'<li><a href="blog_post.php">Publicar</a></li>'."\n";
		echo '<li><a href="blog_list.php">Inicio</a></li>'."\n";
        echo '<li class="current"><a href="blog_edit_user.php">Bem vindo '.$_SESSION['username'].'</a></li>
<li><a href="login/process.logout.php">Sair</a></li>'."\n";
	}
    if($current == "read"){
    echo '<li><a href="blog_post.php">Publicar</a></li>'."\n";
    echo '<li><a href="blog_list.php">Inicio</a></li>'."\n";
    echo $fc;
    }
}
else
{
    echo "\n".'<li><a href="blog_post.php">Publicar</a></li>'."\n";
    echo '<li><a href="blog_list.php">Inicio</a></li>'."\n";
    echo $fc;
}

?>
