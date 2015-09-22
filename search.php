<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 下午3:34
 */
include 'library/init.inc.php';

$keyword = getGET('keyword');

$keyword = $db->escape($keyword);

$get_product_list = 'select `id`,`name`,`price`,`img` from '.$db->table('product').' where `name` like \'%'.$keyword.'%\'';

$product_list = $db->fetchAll($get_product_list);

assign('product_list', $product_list);
assign('keyword', $keyword);

$smarty->display('search.phtml');