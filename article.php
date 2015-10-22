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

$get_article = 'select `wap_content`,`title`,`add_time`,`author`,`section_id` from '.$db->table('content').' where `id`='.$id;
$article = $db->fetchRow($get_article);

assign('article', $article);

$get_section_name = 'select `section_name` from '.$db->table('section').' where `id`='.$article['section_id'];
assign('section_name', $db->fetchOne($get_section_name));

$smarty->display('news-detail.phtml');