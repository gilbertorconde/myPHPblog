<?php

echo '<div id="sidebar_container">';

////////////////////////// Sobre user ////////////////////////////////
/* if(isset($current) && $current === 'read' && isset($post)){ */
/* echo */
/* 	'<img class="paperclip" src="style/paperclip.png" alt="paperclip" /> */
/* 	<div class="sidebar"> */
/* 	<h3>Sobre '.$post['user'].'</h3> */
/* 	'.$post['about_user'].' */
/* 	</div>'; */
/* } */
//////////////////////////////////////////////////////////////////////

////////////////////////// tags //////////////////////////////////////
$tags = get_all_tags($mysqli);
echo 
'

<img class="paperclip" src="style/paperclip.png" alt="paperclip" />
<div class="sidebar">
<h3>Categorias</h3>
<ul>
';
foreach($tags as $tag){
    echo '<li><a href="blog_list.php?tag=' . urlencode($tag) . '">' . $tag . '</a></li>'."\n";
}
echo
'
</ul>
</div>

';

//////////////////////////////////////////////////////////////////////

////////////////////////// RSS ///////////////////////////////////////
echo
'

<img class="paperclip" src="style/paperclip.png" alt="paperclip" />
<div class="sidebar">
<h3>Subscrever</h3>
<p style="text-align:center">
<a href="rss.php"><img src="style/rss.png" alt="RSS Feed" /></a>
</p>
<p style="text-align:center">
<a href="http://feed2.w3.org/check.cgi?url=http%3A//myphpblog.vacau.com/rss.php">
<img src="style/valid-rss-rogers.png" alt="[Valid RSS]" title="Validate my RSS feed" />
</a>
</p>
</div>

';
//////////////////////////////////////////////////////////////////////

////////////////////////// Arquivo ///////////////////////////////////
echo
'
<img class="paperclip" src="style/paperclip.png" alt="paperclip" />
<div class="sidebar">
<h3>Arquivo</h3>
';
$posta = get_posts(false, $mysqli);

$byear = array();
foreach($posta as $pos){
    $aux = explode(' ', $pos['date']);
    $date = $aux[0];
    $aux = explode('/', $date);
    $year = $aux[2];
    $month = $aux[1];
    $byear[$year][$month][$pos['id']] = $aux[0] . " - <a href='blog_read.php?pid={$pos['id']}'>" . html_entity_decode($pos['title']) . '</a>';
}

echo "<ul class=\"ano\" >\n";
foreach($byear as $yearn => $yearo){
    echo "<li class=\"ano\" > Ano: " . $yearn . "</li>\n";
    echo "<ul class=\"mes\"\n>";
    foreach($yearo as $monthn => $monthv){
    echo "<li class=\"mes\" > Mes: " . $monthn . "</li>\n";
    echo "<ul class=\"dia\">\n";
    foreach($monthv as $contets){
        echo "<li class=\"dia\">" . $contets . "</li>\n";
    }
    echo "</ul>\n";
    }
    echo "</ul>\n";
}
echo "</ul>\n";


echo
'
</div>

';
//////////////////////////////////////////////////////////////////////

echo '</div>';

?>
