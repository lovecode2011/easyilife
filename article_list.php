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

$get_article_list = 'select `title`,`id` from '.$db->table('content').' where `section_id`='.$id;
$article_list = $db->fetchAll($get_article_list);

assign('article_list', $article_list);

$smarty->display('article_list.phtml');