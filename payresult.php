<?php
/**
 * 首页
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:11
 */
include 'library/init.inc.php';

$order_sn = $_SESSION['order_sn'];

$get_order_info = 'select * from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\' and `account`=\''.$_SESSION['account'].'\'';

$order = $db->fetchRow($get_order_info);
assign('order', $order);

$get_order_detail = 'select * from '.$db->table('order_detail').' where `order_sn`=\''.$order_sn.'\'';
$order_detail = $db->fetchAll($get_order_detail);
assign('order_detail', $order_detail);

$smarty->display('payresult.phtml');