<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 上午9:17
 */
include 'library/init.inc.php';

//获取用户信息
$get_user_info = 'select * from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$user_info = $db->fetchRow($get_user_info);
assign('user_info', $user_info);

//获取订单数量  待付款  待收货  待评价
$get_order_count = 'select count(*) from '.$db->table('order').' where `account`=\''.$_SESSION['account'].'\' and `status`=';

$order_await_pay = $db->fetchOne($get_order_count.'1');

$order_await_receive = $db->fetchOne($get_order_count.'6');

$order_await_comment = $db->fetchOne($get_order_count.'7');

assign('order_await_pay', $order_await_pay);
assign('order_await_receive', $order_await_receive);
assign('order_await_comment', $order_await_comment);

//获取消费总额
$get_order_sum = 'select sum(`amount`) from '.$db->table('order').' where `account`=\''.$_SESSION['account'].'\'';
$order_sum = $db->fetchOne($get_order_sum);
assign('order_sum', $order_sum);

//获取收藏总数
$get_collection_count = 'select count(*) from '.$db->table('collection').' where `account`=\''.$_SESSION['account'].'\'';
$collection_count = $db->fetchOne($get_collection_count);
assign('collection_count', $collection_count);

//获取猜你喜欢
$get_fav_products = 'select `name`,`price`,`img`,`id` from '.$db->table('product').' order by `add_time` DESC limit 3';
$fav_products = $db->fetchAll($get_fav_products);
assign('fav_products', $fav_products);

$smarty->display('home.phtml');
