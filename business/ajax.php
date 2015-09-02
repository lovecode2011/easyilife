<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-09-02
 * @version 1.0.0
 */

include 'library/init.inc.php';

if( !isset($_SESSION['business_account']) ) {
    echo json_decode(array(
        'error' => 1,
        'message' => '请先登陆',
    ));
    exit;
}

if( check_cross_domain() ) {
    echo json_decode(array(
        'error' => 1,
        'message' => '请从本站提交数据',
    ));
    exit;
}

$operation = 'order_detail';
$opera = check_action($operation, getPOST('opera'));

if( 'order_detail' == $opera ) {

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


    $order_sn = trim(getPOST('sn'));

    if( '' == $order_sn ) {
        echo json_encode(array(
            'error' => 1,
            'message' => '参数错误',
        ));
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select a.*, p.province_name, city.city_name, d.district_name, g.group_name, e.name as express_name from '.$db->table('order').' as a';
    $get_order .= ' left join '.$db->table('province').' as p on a.province = p.id';
    $get_order .= ' left join '.$db->table('city').' as city on a.city = city.id';
    $get_order .= ' left join '.$db->table('district').' as d on a.district = d.id';
    $get_order .= ' left join '.$db->table('group').' as g on a.group = g.id';
    $get_order .= ' left join '.$db->table('express').' as e on a.express_id = e.id';

    $get_order .= ' where `business_account` = \''.$_SESSION['business_account'].'\'';
    $get_order .= ' and order_sn = \''.$order_sn.'\'';
    $get_order .= ' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        echo json_encode(array(
            'error' => 1,
            'message' => '订单不存在',
        ));
        exit;
    }

    $order['add_time_str'] = $order['add_time'] ? date('Y-m-d H:i:s', $order['add_time']) : '';
    $order['delivery_time_str'] = $order['delivery_time'] ? date('Y-m-d H:i:s', $order['delivery_time']) : '未发货';
    $order['receive_time_str'] = $order['receive_time'] ? date('Y-m-d H:i:s', $order['receive_time']) : '未收货';
    $order['pay_time_str'] = $order['pay_time'] ? date('Y-m-d H:i:s', $order['pay_time']) : '未支付';
    $order['status_str'] = $status_str[$order['status']];

    $get_order_detail = 'select o.*, p.img from '. $db->table('order_detail').' as o';
    $get_order_detail .= ' left join '.$db->table('product').' as p on o.product_sn = p.product_sn';
    $get_order_detail .= ' where o.business_account = \''.$_SESSION['business_account'].'\'';
    $get_order_detail .= ' and o.order_sn = \''.$order_sn.'\'';

    $order_detail = $db->fetchAll($get_order_detail);

    echo json_encode(array(
        'error' => 0,
        'order' => $order,
        'order_detail' => $order_detail,
        'message' => '成功',
    ));exit;
}


