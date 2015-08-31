<?php
/**
 * 订单管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-08-24
 * @version 1.0.0
 */

include 'library/init.inc.php';

//商户管理后台初始化
business_base_init();
$template = 'order/';

$action = 'view|edit|delete';
$operation = 'edit';
$act = check_action($action, getGET('act'));
$opera = check_action($operation, getPOST('opera'));
$act = ( $act == '' ) ? 'view' : $act;
//===============================================================================

//===============================================================================

if( 'view' == $act ) {
    if( !check_purview('pur_order_view', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $status_str = array(
        1 => '待支付',
        2 => '支付中',
        3 => '支付完成',
        4 => '待发货',
        5 => '配货中',
        6 => '已发货',
        7 => '已收货',
        8 => '申请退单',
        9 => '退单中',
        10 => '已退单',
        11 => '无效订单',
    );

    $status = intval(getGET('status'));
    if( 0 >= $status || 12 <= $status ) {
        assign('status', 0);
        assign('order_status', '');
        $and_where = '';
    } else {
        assign('status', $status);
        assign('order_status', $status_str[$status]);
        $and_where = ' and status = '.$status;
    }

    $st = trim(getGET('st'));
    $et = trim(getGET('et'));
    $start_time = strtotime($st);
    $end_time = strtotime($et);

    $pattern = '#[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}#';
    if( $st ) {
        if( preg_match($pattern, $st) ) {
            $and_where .= ' and add_time > ' . $start_time;
        } else {
            $st = '';
        }
    }
    if( $et ) {
        if( preg_match($pattern, $et) ) {
            $and_where .= ' and add_time < ' . ($end_time + 3600 * 24);
        } else {
            $st = '';
        }
    }

    //分页参数
    $page = intval(getGET('page'));
    $count = intval(getGET('count'));
    //获取总数
    $get_total = 'select count(*) from '.$db->table('business_exchange_log');
    $get_total .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $total = $db->fetchOne($get_total);

    $page = ( $page > $total ) ? $total : $page;
    $page = ( $page <= 0 ) ? 1 : $page;
    $count = ( $count <= 0 ) ? 10 : $count;
    $offset = ( $page - 1 ) * $count;
    $total_page = ceil( $total / $count );

    $get_order_list = 'select * from '.$db->table('order');
    $get_order_list .= ' where `business_account` = \''.$_SESSION['business_account'].'\'';
    $get_order_list .= $and_where;
    $get_order_list .= 'order by add_time desc';
    $get_order_list .= 'limit '.$offset.','.$count;

    $order_list = $db->fetchAll($get_order_list);

    if( $order_list ) {
        foreach ($order_list as $key => $order) {
            $order_list[$key]['add_time_str'] = date('Y-m-d H:i:s', $order['add_time']);
            $order_list[$key]['delivery_time_str'] = date('Y-m-d H:i:s', $order['delivery_time']);
            $order_list[$key]['receive_time_str'] = date('Y-m-d H:i:s', $order['receive_time']);
            $order_list[$key]['status_str'] = $status_str[$order['status']];
        }
    }


    assign('order_list', $order_list);
    create_pager($page, $total_page, $total);

    assign('count', $count);
    assign('st', $st);
    assign('et', $et);

}


$template .= $act.'.phtml';
$smarty->display($template);