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

$action = 'edit|view|network';
$operation = 'edit|network';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));
//===========================================================================
//修改会员信息
if('edit' == $opera) {
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

    $get_member_list = 'select `id`, `account`, `nickname`, `mobile`, `email`, `add_time`, `leave_time`';
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
        }
    }
    assign('member_list', $member_list);
}


assign('act', $act);
$template .= $act.'.phtml';
$smarty->display($template);