<?php
/**
 * 虚拟订单管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-10-16
 * @version 1.0.0
 */

include 'library/init.inc.php';
back_base_init();

$template = 'virtual_order/';
assign('subTitle', '订单管理');

$action = 'view|detail';
$operation = '';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));

$status_str = array(
    1 => '待支付',
    2 => '支付中',
    3 => '支付完成',
    4 => '订单有效',
    5 => '已完成',
    6 => '已过期',
    7 => '申请退单',
    8 => '已退单',
    9 => '无效订单',
);

//===========================================================================


//===========================================================================

if( 'view' == $act ) {
    if (!check_purview('pur_virtual_order_view', $_SESSION['purview'])) {
        show_system_message('权限不足', array());
        exit;
    }

    $status = intval(getGET('status'));
    if( 0 >= $status || 10 < $status || 3 == $status || 2 == $status ) {
        assign('status', 0);
        assign('order_status', '');
        $and_where = '';
    } else {
        assign('status', $status);
        assign('order_status', $status_str[$status]);
        $and_where = ' and a.status = '.$status;
    }

    $st = trim(getGET('st'));
    $et = trim(getGET('et'));
    $start_time = strtotime($st);
    $end_time = strtotime($et);

    $pattern = '#[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}#';
    if( $st ) {
        if( preg_match($pattern, $st) ) {
            $and_where .= ' and a.add_time > ' . $start_time;
        } else {
            $st = '';
        }
    }
    if( $et ) {
        if( preg_match($pattern, $et) ) {
            $and_where .= ' and a.add_time < ' . ($end_time + 3600 * 24);
        } else {
            $st = '';
        }
    }

    //分页参数
    $page = intval(getGET('page'));
    $count = intval(getGET('count'));
    //获取总数
    $get_total = 'select count(*) from '.$db->table('order').' as a';
    $get_total .= ' where 1';
    $get_total .= $and_where;
    $get_total .= ' and a.is_virtual = 1';    //虚拟产品
    $total = $db->fetchOne($get_total);

    $page = ( $page > $total ) ? $total : $page;
    $page = ( $page <= 0 ) ? 1 : $page;
    $count = ( $count <= 0 ) ? 10 : $count;
    $offset = ( $page - 1 ) * $count;
    $total_page = ceil( $total / $count );

    $get_order_list = 'select a.`order_sn`, a.`add_time`, a.`receive_time`, a.`status`, a.`amount`, a.`integral_amount`, a.`business_account`, a.`product_amount`,';
    $get_order_list .= ' a.`integral_given_amount`, a.`payment_name`, a.`payment_sn`, a.`start_time`, a.`end_time`,a.`consignee`,a.`mobile`, a.`address`,';
    $get_order_list .= ' p.`province_name`, city.`city_name`, d.`district_name`, g.`group_name`,product.`img`, a.`product_name`';
    $get_order_list .= ' from '.$db->table('order').' as a';
    $get_order_list .= ' left join '.$db->table('province').' as p on a.province = p.id';
    $get_order_list .= ' left join '.$db->table('city').' as city on a.city = city.id';
    $get_order_list .= ' left join '.$db->table('district').' as d on a.district = d.id';
    $get_order_list .= ' left join '.$db->table('group').' as g on a.group = g.id';
    $get_order_list .= ' left join '.$db->table('product').' as product on a.product_sn = product.product_sn';
    $get_order_list .= ' where 1';
    $get_order_list .= $and_where;
    $get_order_list .= ' and a.is_virtual = 1';    //虚拟产品
    $get_order_list .= ' order by a.add_time desc';
    $get_order_list .= ' limit '.$offset.','.$count;
    $order_list = $db->fetchAll($get_order_list);
//    echo $get_order_list;exit;

    if( $order_list ) {
        foreach( $order_list as $key => $order ) {
            $order_list[$key]['add_time_str'] = date('Y-m-d H:i:s', $order['add_time']);
            $order_list[$key]['receive_time_str'] = date('Y-m-d H:i:s', $order['receive_time']);
            $order_list[$key]['status_str'] = $status_str[$order['status']];
            $order_list[$key]['start_time_str'] = $order['start_time'] ? date('Y-m-d H:i:s', $order['start_time']) : '';
            $order_list[$key]['end_time_str'] = $order['end_time'] ? date('Y-m-d H:i:s', $order['end_time']) : '';
        }
    }

    assign('order_list', $order_list);
    create_pager($page, $total_page, $total);

    assign('count', $count);
    assign('st', $st);
    assign('et', $et);
}

if( 'detail' == $act ) {
    if( !check_purview('pur_virtual_order_view', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $order_sn = trim(getGET('sn'));

    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select a.*, p.province_name, city.city_name, d.district_name, g.group_name from '.$db->table('order').' as a';
    $get_order .= ' left join '.$db->table('province').' as p on a.province = p.id';
    $get_order .= ' left join '.$db->table('city').' as city on a.city = city.id';
    $get_order .= ' left join '.$db->table('district').' as d on a.district = d.id';
    $get_order .= ' left join '.$db->table('group').' as g on a.group = g.id';
//    $get_order .= ' left join '.$db->table('product').' as e on a.product_sn = e.product_sn';

    $get_order .= ' where 1';
    $get_order .= ' and a.is_virtual = 1';    //虚拟订单
    $get_order .= ' and a.order_sn = \''.$order_sn.'\'';
    $get_order .= ' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }

    $order['add_time_str'] = $order['add_time'] ? date('Y-m-d H:i:s', $order['add_time']) : '';
    $order['receive_time_str'] = $order['receive_time'] ? date('Y-m-d H:i:s', $order['receive_time']) : '未消费';
    $order['pay_time_str'] = $order['pay_time'] ? date('Y-m-d H:i:s', $order['pay_time']) : '未支付';
    $order['expired'] = ($order['start_time'] && $order['end_time']) ? date('Y-m-d H:i:s', $order['start_time']) .'~'. date('Y-m-d H:i:s', $order['end_time']) : '永久有效';
    $order['status_str'] = $status_str[$order['status']];

    $get_content = 'select * from '.$db->table('order_content');
    $get_content .= ' where order_sn = \''.$order_sn.'\'';

    $content = $db->fetchAll($get_content);

    assign('order', $order);
    assign('order_content', $content);

}


$template .= $act.'.phtml';
$smarty->display($template);

