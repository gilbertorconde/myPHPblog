<?php
echo
'
<div class="feedback" id="feedback">
<h3>Comentários:</h3>
<ol class="commentlist">
';

    foreach ($post['comments'] as $comment){
        echo '<li id="comment-'.$comment['comment_id'].'">';
        echo '<h4>Por '.html_entity_decode($comment['user']).'</h4>';
        echo '<h5> ('.$comment['date'].')</h5>';
        echo '<div class="commentbody">';
        echo '<p>'.html_entity_decode($comment['body']).'</p>';
        echo '</div>';
        echo '</li>';
    }

$user = "Anónimo";
if(isset($_SESSION['username'])){
	$user = $_SESSION['username'];
}

    echo
        '
</ol>
<form action="blog_read.php?pid='.$_GET['pid'].'" method="post">
<fieldset>
<legend>Comentar:</legend>
<p>
<label for="user">Nome</label>
<input type="text" name="user" id="user" value="'.$user.'" />
</p>
<p>
<textarea name="body" rows="20" cols="60"></textarea>
</p>
<p>
<input type="submit" value="Enviar" />
</p>
</fieldset>
</form>
</div>
';
?>