<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/1/11
 * Time: 下午7:50
 */
include 'library/init.inc.php';

$get_member_list = 'select r.`id`,r.`account`,m.`add_time` from '.$db->table('member').
                   ' as m join '.$db->table('member').' as r on m.parent_id=r.id where m.`parent_id`>0';
echo $get_member_list;
$member_list = $db->fetchAll($get_member_list);

$target = array();
$target_dest = array();
$data = array();

foreach($member_list as $member)
{
    $integral_send = 0;
    $check_exchange_log = 'select `id`,`integral` from '.$db->table('member_exchange_log').' where `add_time`='.$member['add_time'].' and `account`=\''.$member['account'].'\' and `integral`>0';

    if($exchange_log = $db->fetchRow($check_exchange_log))
    {
        $integral_send = $exchange_log['integral'];
        //echo $exchange_log['id'].'.'.$member['account'].' add recommend integral:+'.$exchange_log['integral'].'<br/>';
    } else {
        $data[] = array(
            'account' => $member['account'],
            'add_time' => $member['add_time'],
            'integral' => $config['recommend_integral'],
            'remark' => '推荐新用户奖励',
            'operator' => 'settle'
        );
    }

    if(!in_array($member['account'], $target))
    {
        $target[] = $member['account'];
        $target_dest[$member['account']] = array(
            'id' => $member['id'],
            'integral' => 0,
            'integral_add' => 0
        );
    }

    if($integral_send)
    {
        $target_dest[$member['account']]['integral'] += $integral_send;
    } else {
        $target_dest[$member['account']]['integral'] += $config['recommend_integral'];
        $target_dest[$member['account']]['integral_add'] += $config['recommend_integral'];
    }
}

if(count($data))
{
    $db->autoInsert('member_exchange_log', $data);
}
echo count($target).'<br/>';
foreach($target_dest as $account=>$integral)
{
    $update_member = 'update '.$db->table('member').' set `integral`=`integral`+'.$integral['integral_add'].' where `account`=\''.$account.'\'';

    $db->update($update_member);
    echo $integral['id'].'.'.$account.':'.$integral['integral'].','.$integral['integral_add'].'<br/>';
}