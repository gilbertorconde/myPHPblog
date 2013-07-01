<?php

echo
"
<header>
<div id=\"header\">
<div id=\"logo\">
	<h1>my<b>PHP</b><a href=\"{$link}\">blog</a></h1>
	<div class=\"slogan\">Living on the Bleeding Edge!!</div>
</div>
<div id=\"menubar\">
<ul id=\"menu\">
";
include('menu.php');
echo
"</ul>
</div>
</div>
</header>
";
?>