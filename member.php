<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 下午4:48
 */
include 'library/init.inc.php';

$get_user_id = 'select `id` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';

$id = $db->fetchOne($get_user_id);

$get_member_list = 'select * from '.$db->table('member').' where `parent_id`='.$id;

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

$smarty->display('member.phtml');