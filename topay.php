<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 上午8:49
 */
include 'library/init.inc.php';

//支付方式变更时生成支付代码

$order_sn = $_SESSION['order_sn'];

$get_order_info = 'select * from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\' and `account`=\''.$_SESSION['account'].'\'';

$order = $db->fetchRow($get_order_info);
assign('order', $order);

$get_order_detail = 'select * from '.$db->table('order_detail').' where `order_sn`=\''.$order_sn.'\'';
$order_detail = $db->fetchAll($get_order_detail);
assign('order_detail', $order_detail);

//获取平台支付方式
$get_payment_list = 'select `id`,`name`,`plugins` from '.$db->table('payment').' where `status`=1';
$payment_list = $db->fetchAll($get_payment_list);
assign('payment_list', $payment_list);
$smarty->display('topay.phtml');