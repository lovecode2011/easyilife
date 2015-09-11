<?php
/**
 * 会员管理
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:04
 */
include 'library/init.inc.php';
back_base_init();

$template = 'member/';
assign('subTitle', '会员管理');

$action = 'edit|view|delete|revoke|network';
$operation = 'edit';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));
//===========================================================================
//修改会员信息
if('edit' == $opera) {
    if( !check_purview('pur_member_edit', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $account = trim(getPOST('account'));
    if( '' == $account ) {
        show_system_message('参数错误', array());
        exit;
    }
    $account = $db->escape($account);
    $get_member = 'select * from '.$db->table('member').' where account = \''.$account.'\' limit 1';
    $member = $db->fetchRow($get_member);
    if( empty($member) ) {
        show_system_message('会员不存在', array());
        exit;
    }

    $mobile = trim(getPOST('mobile'));
    $reward = floatval(getPOST('reward'));
    $integral = floatval(getPOST('integral'));

    if( '' == $mobile ) {
        show_system_message('手机不能为空', array());
        exit;
    }
    if( strlen($mobile) != 11 ) {
        show_system_message('手机格式不正确', array());
        exit;
    }
    $mobile = $db->escape($mobile);

    $reward = ( $reward < 0 ) ? 0 : $reward;
    $integral = ($integral < 0 ) ? 0 : $integral;

    $data = array(
        'mobile' => $mobile,
        'reward' => $reward,
        'integral' => $integral,
    );
    $where = 'account = \''.$account.'\'';
    if( $db->autoUpdate('member', $data, $where) ) {

        if( $reward != $member['reward'] || $integral != $member['integral'] ) {
            $data = array(
                'account' => $account,
                'add_time' => time(),
                'balance' => $member['balance'],
                'reward' => $reward,
                'integral' => $integral,
                'remark' => '修改会员资料',
                'reward_await' => $member['reward_await'],
                'integral_await' => $member['integral_await'],
                'operator' => $_SESSION['account'],
            );
            $db->autoInsert('member_exchange_log', array($data));
        }
        show_system_message('更新成功', array());
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}

//===========================================================================

//
if( 'view' == $act ) {
    if( !check_purview('pur_member_view', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $page = intval(getGET('page'));
    $count = intval(getGET('count'));
    $count_expected = array(10, 25, 50, 100);
    if( !in_array($count, $count_expected) ) {
        $count = 10;
    }

    $get_total = 'select count(*) from '.$db->table('member').' where 1';
    $total = $db->fetchOne($get_total);
    $total_page = ceil($total / $count);

    $page = ( $page > $total_page ) ? $total_page : $page;
    $page = ( $page <= 0 ) ? 1 : $page;

    $offset = ($page - 1) * $count;

    create_pager($page, $total_page, $total);
    assign('count', $count);

    $get_member_list = 'select `id`, `sex`, `account`, `nickname`, `mobile`, `email`, `add_time`, `leave_time`, `status`';
    $get_member_list .= ' from '.$db->table('member');
    $get_member_list .= ' where 1 order by `add_time` desc';
    $get_member_list .= ' limit '.$offset.','.$count;

    $member_list = $db->fetchAll($get_member_list);
    if( $member_list ) {
        foreach ($member_list as $key => $value) {
            if (empty($value['leave_time'])) {
                $member_list[$key]['subscribed'] = '已关注';
            } else {
                $member_list[$key]['subscribed'] = '未关注';
            }
            $member_list[$key]['sex'] = ( $value['sex'] == 'M' ) ? '男' : '女';
        }
    }
    assign('member_list', $member_list);
}

if( 'edit' == $act ) {
    if( !check_purview('pur_member_edit', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $account = trim(getGET('account'));
    if( '' == $account ) {
        show_system_message('参数错误', array());
        exit;
    }
    $account = $db->escape($account);
    $get_member = 'select * from '.$db->table('member').' where account = \''.$account.'\' limit 1';
    $member = $db->fetchRow($get_member);
    if( empty($member) ) {
        show_system_message('会员不存在', array());
        exit;
    }

    assign('member', $member);
}

if( 'delete' == $act ) {
    if( !check_purview('pur_member_del', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $account = trim(getGET('account'));
    if( '' == $account ) {
        show_system_message('参数错误', array());
        exit;
    }
    $account = $db->escape($account);
    $get_member = 'select * from '.$db->table('member').' where account = \''.$account.'\' limit 1';
    $member = $db->fetchRow($get_member);
    if( empty($member) ) {
        show_system_message('会员不存在', array());
        exit;
    }

    $delete_member = 'update '.$db->table('member').' set status = 0  where account = \''.$account.'\' limit 1';
    if( $db->update($delete_member) ) {
        show_system_message('会员'.$account.'已被拉黑', array());
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}


if( 'revoke' == $act ) {
    if( !check_purview('pur_member_del', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $account = trim(getGET('account'));
    if( '' == $account ) {
        show_system_message('参数错误', array());
        exit;
    }
    $account = $db->escape($account);
    $get_member = 'select * from '.$db->table('member').' where account = \''.$account.'\' limit 1';
    $member = $db->fetchRow($get_member);
    if( empty($member) ) {
        show_system_message('会员不存在', array());
        exit;
    }

    $delete_member = 'update '.$db->table('member').' set status = 1  where account = \''.$account.'\' limit 1';
    if( $db->update($delete_member) ) {
        show_system_message('会员'.$account.'已撤销拉黑', array());
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}

if( 'network' == $act ) {
    if( !check_purview('pur_member_network', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $account = trim(getGET('account'));
    if( '' == $account ) {
        show_system_message('参数错误', array());
        exit;
    }
    $account = $db->escape($account);
    $get_member = 'select * from '.$db->table('member').' where account = \''.$account.'\' limit 1';
    $member = $db->fetchRow($get_member);
    if( empty($member) ) {
        show_system_message('会员不存在', array());
        exit;
    }
    if( $member['parent_id'] != 0 ) {
        $get_parent = 'select * from '.$db->table('member').' where id = \''.$member['parent_id'].'\' limit 1';
        $parent = $db->fetchRow($get_parent);

        if( $parent['parent_id'] != 0 ) {
            $get_grand = 'select * from '.$db->table('member').' where id = \''.$parent['parent_id'].'\' limit 1';
            $grand = $db->fetchRow($get_grand);
        }
    }


    $data = array();
    if( !empty($grand) ) {
        $data = array(
            'name' => '上上家-'.$grand['account'],
            'open' => true,
        );
        $data['children'] = array(array(
            'name' => '上家-'.$parent['account'],
            'open' => true,
            'children' => array(array(
                'name' => $member['account'],
            )),
        ));
    } else if( !empty($parent) ) {
        $data = array(
            'name' => '上家-'.$parent['account'],
            'open' => true,
            'children' => array(array(
                'name' => $member['account'],
            )),
        );
    } else {
        $data = array(
            'name' => $member['account'],
        );
    }

    assign('data', json_encode(array($data)));

}

assign('act', $act);
$template .= $act.'.phtml';
$smarty->display($template);