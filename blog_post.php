<?php

include('core/init.inc.php');
include('login/login.func.php');
sec_session_start($mysqli);
$message = "";
if(login_check($mysqli) == true) {

} else {
    $_SESSION['URI'] =  curPageURL();
    header("Location: login/login.php");
    die('Você não está autorizado a acessar esta página. Por favor, efetue login. <br/>');
}

if (isset($_POST['submit'])){

    if (isset($_POST['user'], $_POST['title'], $_POST['body'], $_POST['tags']) && 
        !(empty($_POST['user']) || empty($_POST['title']) || empty($_POST['body']) || empty($_POST['tags']))){
        $aux = explode( ',', $_POST['tags']);
        $tags = array_map('trim',$aux);
        $pid = add_post($_POST['user'], $_POST['title'], $_POST['body'], $tags, $mysqli);
        header('Location: blog_read.php?pid='.$pid);
        die();
    } else {

        $message = "Os campos são todos obrigatórios";

    }
}

echo '<!DOCTYPE HTML>

<html>'; 

include('resources/head.php');

echo 
'
<body>
<div id="main">
';
include('resources/github_ribbons.php');
$current = 'post';
include('resources/header.php');

echo
'
<div id="site_content">
';

include('resources/sidebar.php');

echo
'
<div id="content">
<article>
<header>
<h1>Fazer uma publicação</h1>
<h3 style="color: red" >'.$message.'</h3>
';

echo 
'<form action="" method="post">
<fieldset>
<legend>Publicar:</legend>
<p>
<label for="user">Nome: '.$_SESSION['username'].'</label>
<input type="hidden" name="user" id="user" value="'.$_SESSION['username'].'" />
</p>
<p>
<label for="user">Titúlo</label>
<input type="text" name="title" id="title" />
</p>
<p>
<textarea name="body" rows="20" cols="60"></textarea>
</p>
<p style="text-align: left;">
<label for="user">
Categorias (separadas por virgulas ex: php, html, css).
</label>
<input type="text" name="tags" id="tags" />
</p>
<p>
<input type="submit" name="submit" value="Enviar" />
</p>
</fieldset>
</form>
</header>
</article>
';

include('tinyMCE/init.php');

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

?>
