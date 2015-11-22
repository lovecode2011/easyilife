<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-20
 * @version 
 */

include 'library/init.inc.php';

$get_user = 'select * from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';

$user = $db->fetchRow($get_user);
assign('user', $user);
$path = $user['path'];

$get_member_list = 'select * from '.$db->table('member').' where `path` like \''.$path.'%\' order by (`reward`+`reward_await`) DESC limit 10';

$member_list_tmp = $db->fetchAll($get_member_list);

$member_list = array();
if($member_list_tmp)
{
    foreach ($member_list_tmp as $member)
    {
        $mobile = $member['mobile'];
        $mobile = substr($mobile, 0, 3) . '****' . substr($mobile, -4);

        $member['mobile'] = $mobile;
        $member_list[] = $member;
    }
}

assign('member_list', $member_list);
assign('title', '排行榜');

$smarty->display('ranking.phtml');