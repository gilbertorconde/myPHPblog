<?php

if(isset($post['title'])){
    $head_title = html_entity_decode($post['title']);
}
else {
    $head_title = "MyPHPblog :)";
}

echo
"
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<title>{$head_title}</title>
<meta name=\"description\" content=\"website description\" />
<meta name=\"keywords\" content=\"website keywords, website keywords\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"style/".$_SESSION['style']."/style.css\" />
</head>";
?>