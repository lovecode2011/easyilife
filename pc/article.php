<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/10/10
 * Time: 上午10:06
 */
include 'library/init.inc.php';
$id = intval(getGET('id'));

if($id <= 0)
{
    redirect('index.php');
}

$get_article = 'select `content`,`title`,`add_time`,`author`,`section_id` from '.$db->table('content').' where `id`='.$id;
$article = $db->fetchRow($get_article);

$pattern = '#<img src="([^"]*)"#';
$content = $article['content'];
preg_match_all($pattern, $content ,$matches);
if( $matches ) {
    foreach ($matches[1] as $match) {
        $temp = build_url($match);
        $content = str_replace('"' . $match . '"', '"' . $temp . '"', $content);
    }
}
$article['content'] = $content;

assign('article', $article);

$get_section = 'select `section_name`,`id` from '.$db->table('section').' where `id`='.$article['section_id'];
assign('section', $db->fetchRow($get_section));

$smarty->display('news.phtml');