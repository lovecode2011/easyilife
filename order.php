<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 下午4:47
 */
include 'library/init.inc.php';

$template = 'order_list.phtml';
$action = 'list|detail';
$act = check_action($action, getGET('act'));
$operation = 'pay_now|cancel|rollback|receive';
$opera = check_action($operation, getPOST('opera'));

if('' == $act)
{
    $act = 'list';
}

if('pay_now' == $opera)
{
    $order_sn = getPOST('order_sn');
}

if('detail' == $act)
{
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


    $order_sn = getGET('sn');

    if($order_sn == '')
    {
        redirect('order.php');
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select o.`product_amount`,o.`integral_paid`,o.`balance_paid`,o.`reward_paid`,o.`add_time`,o.`delivery_fee`,o.`pay_time`,o.`order_sn`,b.`shop_name`,o.`status`,o.`amount`,o.`province`,o.`city`,o.`district`,o.`group`,o.`mobile`,o.`consignee`,o.`address` from '.$db->table('order').' as o join '.
                 $db->table('business').' as b using(`business_account`) where o.`account`=\''.$_SESSION['account'].'\' and o.`order_sn`=\''.$order_sn.'\'';

    $order = $db->fetchRow($get_order);

    $get_order_detail = 'select od.`product_attributes`,od.`product_price`,od.`product_name`,od.`product_sn`,p.`id`,p.`img`,od.`count` from '.$db->table('order_detail').' as od '.
            ' join '.$db->table('product').' as p using(`product_sn`) where od.`order_sn`=\''.$order_sn.'\'';

    $order['order_detail'] = $db->fetchAll($get_order_detail);
    $order['show_status'] = $status_str[$order['status']];

    $get_province_name = 'select `province_name` from '.$db->table('province').' where `id`='.$order['province'];
    $get_city_name = 'select `city_name` from '.$db->table('city').' where `id`='.$order['city'];
    $get_district_name = 'select `district_name` from '.$db->table('district').' where `id`='.$order['district'];
    $get_group_name = 'select `group_name` from '.$db->table('group').' where `id`='.$order['group'];

    $order['province_name'] = $db->fetchOne($get_province_name);
    $order['city_name'] = $db->fetchOne($get_city_name);
    $order['district_name'] = $db->fetchOne($get_district_name);
    $order['group_name'] = $db->fetchOne($get_group_name);

    assign('order', $order);
    $template = 'order_detail.phtml';
    $_SESSION['order_sn'] = $order_sn;
}

if('list' == $act)
{
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

    assign('status', $status);

    $get_order_list = 'select o.`order_sn`,b.`shop_name`,o.`status`,o.`amount`,o.`business_account` from '.$db->table('order').' as o join '.
                      $db->table('business').' as b using(`business_account`) where o.`account`=\''.$_SESSION['account'].'\'';

    if($status > 0 && $status < 8)
    {
        $get_order_list .= ' and o.`status`='.$status;
    }

    if($status > 0 && $status >= 8)
    {
        $get_order_list .= ' and o.`status`>='.$status;
    }

    $get_order_list .= ' order by o.`add_time` DESC';

    $order_list = $db->fetchAll($get_order_list);

    if($order_list)
    {
        foreach ($order_list as $key => $ol)
        {
            $get_order_detail = 'select od.`product_attributes`,od.`product_name`,od.`product_sn`,p.`id`,p.`img`,od.`count` from ' . $db->table('order_detail') . ' as od ' .
                ' join ' . $db->table('product') . ' as p using(`product_sn`) where od.`order_sn`=\'' . $ol['order_sn'] . '\'';
            $order_list[$key]['order_detail'] = $db->fetchAll($get_order_detail);
            $order_list[$key]['show_status'] = $status_str[$ol['status']];
        }
    }

    assign('order_list', $order_list);
}

$smarty->display($template);