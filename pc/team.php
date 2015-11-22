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
$id = $user['id'];

$get_member_list = 'select * from '.$db->table('member').' where `parent_id`='.$id.' order by (`reward`+`reward_await`) DESC';

$member_list_tmp = $db->fetchAll($get_member_list);

$first_member_list = array();
$second_member_list = array();
$member_id_array = array();
if($member_list_tmp)
{
    foreach ($member_list_tmp as $member)
    {
        array_push($member_id_array, $member['id']);

        $mobile = $member['mobile'];
        $mobile = substr($mobile, 0, 3) . '****' . substr($mobile, -4);

        $member['mobile'] = $mobile;
        $first_member_list[] = $member;
    }

    $member_ids = implode(',', $member_id_array);

    $get_member_list = 'select * from '.$db->table('member').' where `parent_id` in ('.$member_ids.') order by (`reward`+`reward_await`) DESC';
    $member_list_tmp = $db->fetchAll($get_member_list);

    if( $member_list_tmp ) {
        foreach ($member_list_tmp as $m)
        {
            $mobile = $m['mobile'];
            $mobile = substr($mobile, 0, 3) . '****' . substr($mobile, -4);

            $m['mobile'] = $mobile;
            $second_member_list[] = $m;
        }
    }
}

assign('first_member_list', $first_member_list);
assign('second_member_list', $second_member_list);
assign('title', '我的团队');

$level = array(
    0 => '普通会员',
    1 => '合伙人',
    2 => '高级合伙人'
);
assign('level', $level);

$smarty->display('team.phtml');