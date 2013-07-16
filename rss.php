<?php

include('core/init.inc.php');
include('login/login.func.php');
sec_session_start($mysqli);

$doc = new DomDocument('1.0', 'UTF-8');

//to have indented output, not just a line
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

// ------------- Interresting part here ------------

//creating an xslt adding processing line
$xslt = $doc->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="http://myphpblog.vacau.com/style/'.$_SESSION['style'].'/rss.xsl"');

//adding it to the xml
$doc->appendChild($xslt);

$rss = $doc->createElement('rss');

$r_version = $doc->createAttribute('version');
$r_version->value = '2.0';
$r_atom = $doc->createAttribute('xmlns:atom');
$r_atom->value = 'http://www.w3.org/2005/Atom';

$rss->appendChild($r_version);
$rss->appendChild($r_atom);

$doc->appendChild($rss);

$channel = $doc->createElement('channel');
$c_atom = $doc->createElementNS('http://www.w3.org/2005/Atom', 'atom:link');

$at_href = $doc->createAttribute('href');
$at_href->value = 'http://myphpblog.vacau.com/rss.php';
$at_rel = $doc->createAttribute('rel');
$at_rel->value = 'self';
$at_type = $doc->createAttribute('type');
$at_type->value = 'application/rss+xml';
$c_atom->appendChild($at_href);
$c_atom->appendChild($at_rel);
$c_atom->appendChild($at_type);

$c_title = $doc->createElement('title', 'MyPHPBlog');
$c_link = $doc->createElement('link', htmlentities('http://myphpblog.vacau.com'));
$c_description = $doc->createElement('description', 'dedicado ao desenvolvimento de um blog em php');
$c_language = $doc->createElement('language', $_SESSION['language']);
$c_copyright = $doc->createElement('copyright', 'Copyleft :) 2013 Gilberto Conde');

$rss->appendChild($channel);
$channel->appendChild($c_atom);
$channel->appendChild($c_title);
$channel->appendChild($c_link);
$channel->appendChild($c_description);
$channel->appendChild($c_language);
$channel->appendChild($c_copyright);


$result = get_posts(true, $mysqli);
$users = get_users($mysqli);
foreach($result as $row) {
    extract($row);
    $email = "";
    $pid = $id;
    if(isset($user)){
        foreach($users as $use) {
            extract($use);
            if($user_name === $user){
                $email = $user_email;
                break;
            }
        }
    }
    
    $html = new DOMDocument('1.0');
    $searchPage = mb_convert_encoding($preview, 'HTML-ENTITIES', "UTF-8");
    @$html->loadHTML($searchPage);
    // htmlentities();
    $elements_array = array();
    $elements_array[0] = $html->getElementsByTagName('img');
    $elements_array[1] = $html->getElementsByTagName('a');
    $elements_array[2] = $html->getElementsByTagName('ul');
    $elements_array[3] = $html->getElementsByTagName('p');
    
    foreach($elements_array as $elements){
        foreach($elements as $element){
            $element->removeAttribute('data-mce-src');
            $element->removeAttribute('data-mce-href');
            $element->removeAttribute('data-mce-style');
        }
    }

    $item = $doc->createElement('item');
    $channel->appendChild($item);

    $i_title = $doc->createElement('title', html_entity_decode($title));
    $i_description = $doc->createElement('description');
    $c_data = $doc->createCDATASection(strip_tags($html->saveHTML(), '<b><em><br><ol><li><ul><img><p><a>'));
    $i_description->appendChild($c_data);
    $i_author = $doc->createElement('author', $email . ' (' . $user . ')');
    $i_link = $doc->createElement('link', htmlentities('http://myphpblog.vacau.com/blog_read.php?pid=' . $pid));
    $i_guid = $doc->createElement('guid', htmlentities('http://myphpblog.vacau.com/blog_read.php?pid=' . $pid));
    $i_pubDate = $doc->createElement('pubDate', date("D, d M Y H:i:s O", strtotime($date)));
    
    $item->appendChild($i_title);
    $item->appendChild($i_description);
    $item->appendChild($i_author);
    $item->appendChild($i_link);
    $item->appendChild($i_guid);
    $item->appendChild($i_pubDate);
    
    $tags = get_tags($pid, $mysqli);
    if(isset($tags) && !(empty($tags))){
        foreach($tags as $key_tag => $tag){
            $i_category = $doc->createElement('category');
            $i_c_data = $doc->createCDATASection($tag);
            $i_category->appendChild($i_c_data);
            $item->appendChild($i_category);
        }
    }
}

//header("Content-Type: application/rss+xml; charset=UTF-8");
header("Content-Type: text/xml; charset=UTF-8");
echo $doc->saveXML();

?>