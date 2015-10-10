<?php
/**
 * 首页
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:11
 */
include 'library/init.inc.php';

//获取用户信息
$get_user_info = 'select * from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$user_info = $db->fetchRow($get_user_info);
assign('user_info', $user_info);

//获取公告
$get_notice = 'select `id`,`title` from '.$db->table('content').' where `section_id`=1 order by `add_time` DESC limit 5';
$notice = $db->fetchAll($get_notice);
assign('notice', $notice);

$smarty->display('index.phtml');
