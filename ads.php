<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/11/16
 * Time: 下午4:45
 */
include 'library/init.inc.php';

$get_ad_list = 'select `img`,`url` from '.$db->table('ad').' where `ad_pos_id`=11 order by `order_view`';

$ad_list = $db->fetchAll($get_ad_list);

assign('title', '商学院');
assign('ad_list', $ad_list);
$smarty->display('ads.phtml');