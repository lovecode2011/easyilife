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

$get_article = 'select `content`,`title`,`add_time`,`author` from '.$db->table('content').' where `id`='.$id;
$article = $db->fetchRow($get_article);

assign('article', $article);

$smarty->display('article.phtml');