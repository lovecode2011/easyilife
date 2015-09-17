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

$smarty->display('user.phtml');