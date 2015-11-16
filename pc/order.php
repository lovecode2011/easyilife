<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-16
 * @version 
 */

include 'library/init.inc.php';

$action = 'list|detail|comment|express_info|product_comment';
$act = check_action($action, getGET('act'));
$operation = 'pay_now|cancel|rollback|receive|comment|product_comment|sort';
$opera = check_action($operation, getPOST('opera'));

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
    12 => '已完成'
);


if('' == $act)
{
    $act = 'list';
}

if( $act == 'list' ) {
    $status = intval(getGET('status'));
    $status = $status < 0 || $status > 12 ? 0 : $status;

    $where = '';
    switch ($status) {
        case 1:
            $where .= ' and o.status = 1';
            break;
        case 6:
            $where .= ' and o.status = 6';
            break;
        case 7:
            $where .= ' and o.status = 7';
            break;
        case 8:
            $where .= ' and o.status = 8';
            break;
        case 12:
            $where .= ' and o.status = 12';
            break;
        default :
            $where .= '';
            break;
    }

    $get_all_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\'';
    $all_total = $db->fetchOne($get_all_total);

    $get_pay_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 1';
    $pay_total = $db->fetchOne($get_pay_total);

    $get_receive_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 6';
    $receive_total = $db->fetchOne($get_receive_total);

    $get_comment_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 7';
    $comment_total = $db->fetchOne($get_comment_total);

    $get_refund_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 8';
    $refund_total = $db->fetchOne($get_refund_total);

    $get_complete_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 12';
    $complete_total = $db->fetchOne($get_complete_total);

    $get_order_list = 'select o.`order_sn`,b.`shop_name`,o.`status`,o.`amount`,o.`business_account`,o.`add_time` from '.$db->table('order').' as o join '.
        $db->table('business').' as b using(`business_account`) where o.`account`=\''.$_SESSION['account'].'\'';
    $get_order_list .= $where;

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

    assign('status', $status);
    assign('all_total', $all_total);
    assign('pay_total', $pay_total);
    assign('receive_total', $receive_total);
    assign('comment_total', $comment_total);
    assign('refund_total', $refund_total);
    assign('complete_total', $complete_total);
}

$template = 'order.phtml';
$smarty->display($template);