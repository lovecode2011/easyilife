<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-19
 * @version
 */

include 'library/init.inc.php';
$page_count = 1;
assign('page_count', $page_count);
$operation = 'paging';
$opera = check_action($operation, getPOST('opera'));

if( 'paging' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    if(!check_cross_domain() && isset($_SESSION['account']) ) {
        $page = intval(getPOST('page'));
        $status = intval(getPOST('status'));
        $where = '`account`=\''.$_SESSION['account'].'\'';
        switch( $status ) {
            case 1: $where .= ' and integral > 0'; break;
            case -1: $where .= ' and integral < 0'; break;
            default: $status = 0; $where .= ' and integral <> 0'; break;
        }
        assign('status', $status);

        $get_total = 'select count(*) from '.$db->table('member_exchange_log').' where '.$where;
        $total = $db->fetchOne($get_total);

        $total_page = ceil( $total / $page_count );

        $page = $page > $total_page ? $total_page : $page;
        $page = $page < 1 ? 1 : $page;
        $offset = $page_count * ( $page - 1 );

        //获取收支明细
        $get_member_exchange = 'select * from '.$db->table('member_exchange_log').' where '.$where;
        $get_member_exchange .= ' order by `add_time` DESC';
        $get_member_exchange .= ' limit '.$offset.','.$page_count;
        $member_exchange = $db->fetchAll($get_member_exchange);

        if( $member_exchange ) {
            $response['error'] = 0;
            assign('member_exchange', $member_exchange);
            assign('page', $page);
            assign('total_page', $total_page);
            $response['content'] = $smarty->fetch('integral-item.phtml');
        } else {
            $response['msg'] = '系统繁忙，请稍后重试';
        }

    } else {
        if (empty($_SESSION['account'])) {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }
    echo json_encode($response);
    exit;
}

//获取用户信息
$get_user_info = 'select * from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$user_info = $db->fetchRow($get_user_info);
assign('user_info', $user_info);

//获取收支明细
$get_member_exchange = 'select * from '.$db->table('member_exchange_log').' where `account`=\''.$_SESSION['account'].
    '\' and `integral`<>0 order by `add_time` DESC';

$member_exchange = $db->fetchAll($get_member_exchange);

$total = count($member_exchange);
$total_page = ceil($total / $page_count);
assign('total_page', $total_page);

assign('member_exchange', $member_exchange);

$smarty->display('integral.phtml');