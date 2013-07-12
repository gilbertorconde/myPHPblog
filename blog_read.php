<?php

include('core/init.inc.php');
include('login/login.func.php');
sec_session_start($mysqli);

$user_loged = false;
$div_class = 'notEdit';
if(login_check($mysqli) == true) {
    $user_loged = true;
}
// Removes the actual entry
if(isset($_POST['remove_post'])){
    $post = get_post($_GET['pid'], $mysqli); 
    if($user_loged && ($post['user'] == $_SESSION['username'])){
        remove_post($_GET['pid'], $mysqli);
        header("Location: blog_list.php");
        die();
    } else {
        $message = "Não pode apagar esta entrada.";
    }
}

// Add coment to actual entry
if (isset($_GET['pid'], $_POST['user'], $_POST['body'])){
    if (add_comment($_GET['pid'], $_POST['user'], $_POST['body'], $mysqli)){
    }
    else {
        header("Location: blog_list.php");
        die();
    }
}

// Get Content from actual entry
if (isset($_GET['pid']) && valid_pid($_GET['pid'], $mysqli)){
    $post = get_post($_GET['pid'], $mysqli);
}

echo '<!DOCTYPE HTML>

<html>'; 

include('resources/head.php');

echo 
'
<body>
<div id="socialMessage" class="message" style="color: green;"></div>
<div id="main">
';
include('resources/github_ribbons.php');
$current = 'read';
include('resources/header.php');

echo
'
<div id="site_content">
';

include('resources/sidebar.php');

echo
'
<div id="content">
<section>
<article>
<header>
';

if (!isset($post)){
    echo "ID de Post invalido.";
}
else{
    $mess = "";
    if($user_loged && ($post['user'] == $_SESSION['username'])){
	$div_class = 'edit';
	$mess = "
<form action=\"\" method=\"post\" onsubmit=\"return confirm('Pretende apagar esta publicação?')\" >
<input type=\"submit\" name=\"remove_post\" id=\"remove_post\" value=\"Remover entrada\" />
</form><br />
<h5>Para editar clique sobre o título ou texto</h5>";
    }
    echo "<h1 class=\"".$div_class."\" id=\"title\" >".html_entity_decode($post['title'])."</h1>";
    $dat = explode(' ', $post['date']);
    $hora = $dat[1];
    $data = $dat[0];
    echo "<h5> Data: <time datetime=\"".$hora."\">".$data
        ."</time> por <a href=\"blog_list.php?user=".$post['user']."\">".$post['user']
        ."</a> - <a href=\"#feedback\">".count($post['comments'])
        ." comentário(s)</a></h5> ".$mess;
    echo "</header>";
    echo "<div class=\"".$div_class."\" id=\"bodyy\" >".$post['body']."</div>";

    $tags = get_tags($_GET['pid'], $mysqli);
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

    echo
        '</article>
</section>
';
    include('resources/about.php');
    
    include('resources/feedback.php');
}
if($user_loged){
echo
'
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="tinyMCE/tinymce.min.js"></script>
<script src="resources/utf8_encode.js"></script>
<script>
tinymce.init({
    selector: "div.edit",
    language: "pt_BR",
    theme: "modern",
    plugins: [
        ["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker"],
        ["searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking"],
        ["save table contextmenu directionality emoticons template paste"]
    ],
    add_unload_trigger: false,
    schema: "html5",
    inline: true,
    toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image     | print preview media",
    statusbar: false,
    setup: function(editor) {
        editor.on(\'blur\', function(e){ postBody(); });
    }

});

tinymce.init({
    selector: "h1.edit",
    language: "pt_BR",
    inline: true,
    toolbar: "undo redo",
    statusbar: false,
    setup: function(editor) {
        editor.on(\'blur\', function(e){ postTitle(); });
    }
});
</script>
<script>
function postTitle(){
    if(tinymce.get(\'title\').isDirty()){
        var title = tinymce.get(\'title\').getElement().innerHTML;
        sendSocialForm(\'title\', title);
        document.getElementById(\'socialMessage\').style.display=\'block\';
        document.getElementById(\'socialMessage\').innerHTML=\'<h2>A guardar, espere...</h2>\';
    }
}

function postBody(){
        if(tinymce.get(\'bodyy\').isDirty()){
        var body = tinymce.get(\'bodyy\').getElement().innerHTML;
        sendSocialForm(\'body\', body);
        document.getElementById(\'socialMessage\').style.display=\'block\';
        document.getElementById(\'socialMessage\').innerHTML=\'<h2>A guardar, espere...</h2>\';
    }
}

function removeComment( cid ){
    if(confirm(\'Pretende apagar esta comentário?\')){
        strToPost = "cid="+cid;
        element = document.getElementById(\'comment-\'+cid);
        element.parentNode.removeChild(element);
        makePostRequest(strToPost, "application/x-www-form-urlencoded");
        return true;
    }
    return false;
}

function sendSocialForm(param, title) {
    strToPost = param+"="+title+"&pid='.$_GET['pid'].'";
    makePostRequest(strToPost, "application/x-www-form-urlencoded");
}

function makePostRequest(strToPost, ctpy ){
    if (!ctpy){
	ctpy = "application/x-www-form-urlencoded";
    }
    xmlhttp = null;
    if (window.XMLHttpRequest) {// code for IE7, Firefox, Opera, etc.
        xmlhttp=new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (xmlhttp!=null) {
	xmlhttp.onreadystatechange=state_Change;
	xmlhttp.open("POST","resources/inline_post.php",true);
	xmlhttp.setRequestHeader("Content-type",ctpy);
	xmlhttp.send(strToPost);
    }
    else {
        alert("Your browser does not support XMLHTTP.");
    }
}

function state_Change()
{
    if (xmlhttp.readyState==4){
        if (xmlhttp.status==200){
            //if(xmlhttp.responseText != \'NULL\'){
                //values = xmlhttp.responseText.split("|");
                //alert(xmlhttp.responseText);
            //}
            document.getElementById(\'socialMessage\').style.display=\'block\';
            document.getElementById(\'socialMessage\').innerHTML=\'<h2>Alterações guardadas!</h2>\';
            setTimeout(function(){
                document.getElementById(\'socialMessage\').innerHTML=\'\';
                document.getElementById(\'socialMessage\').style.display=\'none\';
            },3000);
        }
        else {
            alert("Problem retrieving ata:" + xmlhttp.statusText);
            return false;
        }
    }
}
</script>
';
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

if(isset($message)){
    echo
        '
document.getElementById(\'socialMessage\').style.display=\'block\';
            document.getElementById(\'socialMessage\').innerHTML=\'<h2>'.$message.'</h2>\';
            setTimeout(function(){
                document.getElementById(\'socialMessage\').innerHTML=\'\';
                document.getElementById(\'socialMessage\').style.display=\'none\';
            },3000);
';
}

?>
