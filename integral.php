<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/16
 * Time: 上午1:54
 */
include 'library/init.inc.php';

//获取用户信息
$get_user_info = 'select * from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$user_info = $db->fetchRow($get_user_info);
assign('user_info', $user_info);

//获取明细
$get_member_exchange = 'select * from '.$db->table('member_exchange_log').' where `account`=\''.$_SESSION['account'].
                        '\' and `integral`<>0 order by `add_time` DESC';

$member_exchange = $db->fetchAll($get_member_exchange);
assign('member_exchange', $member_exchange);

$smarty->display('integral.phtml');