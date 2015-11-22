<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-16
 * @version 
 */

include 'library/init.inc.php';

$action = 'list|detail|comment|product_comment';
$act = check_action($action, getGET('act'));
$operation = 'pay_now|cancel|rollback|receive|comment|product_comment|sort|paging|express_info';
$opera = check_action($operation, getPOST('opera'));

$template = 'order.phtml';

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
$page_count = 1;




if('' == $act)
{
    $act = 'list';
}

if('product_comment' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $product_sn = getPOST('product_sn');
    $star = getPOST('star');
    $comment = getPOST('comment');

    if($product_sn == '')
    {
        $response['msg'] = '参数错误';
    } else {
        $product_sn = $db->escape($product_sn);
    }

    $star = intval($star);
    if($star <= 0)
    {
        $response['msg'] .= '-请选择星级<br/>';
    }

    if($comment == '')
    {
        $response['msg'] .= '-请填写评价<br/>';
    } else {
        $comment = htmlspecialchars($comment);
        $comment = $db->escape($comment);
    }

    if($response['msg'] == '')
    {
        $comment_data = array(
            'add_time' => time(),
            'comment' => $comment,
            'star' => $star,
            'product_sn' => $product_sn,
            'account' => $_SESSION['account'],
            'parent_id' => 0
        );

        if($db->autoInsert('comment', array($comment_data)))
        {
            $id = $db->get_last_id();

            $comment_data = array('path'=>$id.',');

            $db->autoUpdate('comment', $comment_data, '`id`='.$id);

            //获取评论数量和星级
            $get_comment_info = 'select count(*) as c, sum(`star`) as star from '.$db->table('comment').' where `product_sn`=\''.$product_sn.'\'';
            $comment_info = $db->fetchRow($get_comment_info);
            $product_data = array('star'=>$comment_info['star']/$comment_info['c']);

            $db->autoUpdate('product', $product_data, '`product_sn`=\''.$product_sn.'\'');

            $response['error'] = 0;
            $response['msg'] = '评论成功';
        } else {
            $response['msg'] = '系统繁忙，请稍后再试';
        }
    }

    echo json_encode($response);
    exit;
}

if('comment' == $opera)
{
    $order_sn = getPOST('order_sn');
    $star = getPOST('star');
    $star = intval($star);

    $response = array('error'=>1, 'msg'=>'');

    if($order_sn == '')
    {
        $response['msg'] = '订单编号为空';
    } else {
        $order_sn = $db->escape($order_sn);
        $check_business_account = 'select `business_account` from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\''.$_SESSION['account'].'\'';
        $business_account = $db->fetchOne($check_business_account);

        if($business_account)
        {
            //获取历史已评价订单数量
            $get_comment_order = 'select count(*) from '.$db->table('order').' where `business_account`=\''.$business_account.'\' and `is_comment`=1';
            $comment_order = $db->fetchOne($get_comment_order);

            //获取店铺当前评价
            $get_comment = 'select `comment` from '.$db->table('business').' where `business_account`=\''.$business_account.'\'';
            $comment = $db->fetchOne($get_comment);

            $comment = $comment*$comment_order;
            $comment_order++;
            $comment += $star;

            $comment = $comment/$comment_order;

            $data = array('comment'=>$comment);

            if($db->autoUpdate('business', $data, '`business_account`=\''.$business_account.'\''))
            {
                $order_data = array('is_comment'=>1, 'status'=>12);

                if($db->autoUpdate('order', $order_data, '`order_sn`=\''.$order_sn.'\''))
                {
                    add_order_log($order_sn, $_SESSION['account'], 12, '用户评价订单');
                    $response['msg'] = '服务评价成功';
                    $response['error'] = 0;
                } else {
                    $response['msg'] = '系统繁忙，请稍后再试';
                }
            } else {
                $response['msg'] = '系统繁忙，请稍后再试';
            }
        } else {
            $response['msg'] = '订单错误';
        }
    }

    echo json_encode($response);
    exit;
}

if('rollback' == $opera)
{
    $order_sn = getPOST('order_sn');

    $response = array('error'=>1, 'msg'=>'');

    if($order_sn == '')
    {
        $response['msg'] = '订单编号为空';
    } else {
        $order_sn = $db->escape($order_sn);
        $check_order_sn = 'select `order_sn` from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\''.$_SESSION['account'].'\'';
        $order_sn = $db->fetchOne($check_order_sn);

        if($order_sn)
        {
            $data = array('status'=>8);

            if($db->autoUpdate('order', $data, '`order_sn`=\''.$order_sn.'\''))
            {
                add_order_log($order_sn, $_SESSION['account'], 8, '用户申请退单');
                //将款项打到商家账户中
                $response['error'] = 0;
                $response['msg'] = '申请退单成功，稍后商家客服将直接与您联系';
            } else {
                $response['msg'] = '系统繁忙，请稍后再试';
            }
        } else {
            $response['msg'] = '订单错误';
        }
    }

    echo json_encode($response);
    exit;
}

if('receive' == $opera)
{
    $order_sn = getPOST('order_sn');

    $response = array('error'=>1, 'msg'=>'');

    if($order_sn == '')
    {
        $response['msg'] = '订单编号为空';
    } else {
        $order_sn = $db->escape($order_sn);
        $check_order_sn = 'select `order_sn` from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\''.$_SESSION['account'].'\'';
        $order_sn = $db->fetchOne($check_order_sn);

        if($order_sn)
        {
            $data = array('status'=>7);

            if($db->autoUpdate('order', $data, '`order_sn`=\''.$order_sn.'\''))
            {
                add_order_log($order_sn, $_SESSION['account'], 7, '用户确认收货');
                //将款项打到商家账户中
                //读取担保交易记录，并将款项打入到商家的余额中
                $get_trade = 'select `id`,`business_account`,`trade` from '.$db->table('trade').' where `remark`=\''.$order_sn.'\' and `status`=0';
                $trade = $db->fetchRow($get_trade);

                if(add_business_exchange($trade['business_account'], $trade['trade'], -1*$trade['trade'], $_SESSION['account'], '用户确认收货'))
                {
                    $trade_status = array(
                        'status' => 1,
                        'solve_time' => time()
                    );

                    $db->autoUpdate('trade', $trade_status, '`id`='.$trade['id']);
                }
                $response['error'] = 0;
                $response['msg'] = '确认收货成功';
            } else {
                $response['msg'] = '系统繁忙，请稍后再试';
            }
        } else {
            $response['msg'] = '订单错误';
        }
    }

    echo json_encode($response);
    exit;
}

if('cancel' == $opera)
{
    $order_sn = getPOST('order_sn');

    $response = array('error'=>1, 'msg'=>'');

    if($order_sn == '')
    {
        $response['msg'] = '订单编号为空';
    } else {
        $order_sn = $db->escape($order_sn);
        $check_order_sn = 'select `order_sn` from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\''.$_SESSION['account'].'\'';
        $order_sn = $db->fetchOne($check_order_sn);

        if($order_sn)
        {
            $db->begin();
            //回退库存
            //回退积分/佣金/余额
            //删除订单
            if($db->autoDelete('order', '`order_sn`=\''.$order_sn.'\''))
            {
                if($db->autoDelete('order_detail', '`order_sn`=\''.$order_sn.'\''))
                {
                    $response['error'] = 0;
                    $response['msg'] = '订单取消成功';
                    $db->commit();
                } else {
                    $db->rollback();
                    $response['msg'] = '002:取消订单失败';
                }
            } else {
                $response['msg'] = '001:取消订单失败';
                $db->rollback();
            }
        } else {
            $response['msg'] = '订单错误';
        }
    }

    echo json_encode($response);
    exit;
}

if('pay_now' == $opera)
{
    $order_sn = getPOST('order_sn');

    $response = array('error'=>1, 'msg'=>'');

    if($order_sn == '')
    {
        $response['msg'] = '订单编号为空';
    } else {
        $order_sn = $db->escape($order_sn);
        $check_order_sn = 'select `order_sn` from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\''.$_SESSION['account'].'\'';
        $order_sn = $db->fetchOne($check_order_sn);

        if($order_sn)
        {
            $_SESSION['order_sn'] = $order_sn;
            $response['error'] = 0;
        } else {
            $response['msg'] = '订单错误';
        }
    }

    echo json_encode($response);
    exit;
}

if('product_comment' == $act)
{
    $product_sn = getGET('product_sn');

    if($product_sn == '')
    {
        redirect('index.php');
    }

    $template = 'product_comment.phtml';

    $get_product = 'select `name`,`product_sn`,`img` from '.$db->table('product').' where `product_sn`=\''.$product_sn.'\'';

    $product = $db->fetchRow($get_product);

    assign('product', $product);
}

if('express_info' == $opera)
{
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    $express_state = array(
        0 => '在途',
        1 => '揽件',
        2 => '疑难',
        3 => '签收',
        4 => '退签',
        5 => '派件',
        6 => '退回'
    );
    assign('express_state', $express_state);

    $order_sn = getPOST('order_sn');

    if($order_sn == '')
    {
        $response['msg'] = '参数错误';
    } else {
        $order_sn = $db->escape($order_sn);
    }

    $get_order_info = 'select * from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\'';
    $order = $db->fetchRow($get_order_info);

    if($order && $order['express_sn'] )
    {
        $get_express_info = 'select `code`,`name` from '.$db->table('express').' where `id`='.$order['express_id'];
        $express_info = $db->fetchRow($get_express_info);
        $express_flow = query_express($express_info['code'], $order['express_sn']);
        $express_flow = json_decode($express_flow, true);
        $response['error'] = 0;
        $response['content'] = $express_flow;
    }
    echo json_encode($response);
    exit;


}

if('comment' == $act)
{
    $template = 'comment.phtml';

    $order_sn = getGET('sn');

    if($order_sn == '')
    {
        redirect('order.php');
    }
    $order_sn = $db->escape($order_sn);
    $check_order_sn = 'select `order_sn` from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\''.$_SESSION['account'].'\'';
    $order_sn = $db->fetchOne($check_order_sn);
    if(!$order_sn)
    {
        redirect('order.php');
    }


    $check_order_comment = 'select o.`is_comment`, o.`business_account`, o.`order_sn`, o.`add_time`, b.`shop_name`,b.`id` as bid from '.$db->table('order').' as o';
    $check_order_comment .= ' left join '.$db->table('business').' as b on o.business_account = b.business_account';
    $check_order_comment .= ' where o.`order_sn`=\''.$order_sn.'\' limit 1';
    $order = $db->fetchRow($check_order_comment);
    assign('order', $order);

    $get_order_detail = 'select DISTINCT od.`product_name`,od.`product_sn`,p.`img`,p.`id`,od.`product_attributes`,od.`count`,od.product_price, od.integral from '.$db->table('order_detail').' as od '.
        ' join '.$db->table('product').' as p using(`product_sn`) where od.`order_sn`=\''.$order_sn.'\'';

    $order_detail = $db->fetchAll($get_order_detail);

    foreach($order_detail as $key=>$od)
    {
        $check_product_comment = 'select c.`id` from '.$db->table('comment').' as c';
        $check_product_comment .= ' left join '.$db->table('order_detail').' as od on c.product_sn = od.product_sn';
        $check_product_comment .= ' where od.`product_sn`=\''.$od['product_sn'].
            '\' and c.`account`=\''.$_SESSION['account'].'\' and od.order_sn = \''.$order_sn.'\'';
        $order_detail[$key]['has_comment'] = $db->fetchOne($check_product_comment);
    }
    assign('order_sn', $order_sn);

    assign('order_detail', $order_detail);
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
        12 => '已完成',
    );


    $order_sn = getGET('sn');

    if($order_sn == '')
    {
        redirect('order.php');
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select o.`remark`,o.`product_amount`,o.`integral_paid`,o.`balance_paid`,o.`reward_paid`,o.`add_time`,o.`delivery_fee`,o.`pay_time`,o.`order_sn`,b.`shop_name`,o.`status`,o.`amount`,o.`province`,o.`city`,o.`district`,o.`group`,o.`mobile`,o.`consignee`,o.`address`,o.`integral_amount`,o.`express_sn`,o.`express_id`,o.zipcode,b.`id` as bid from '.$db->table('order').' as o join '.
        $db->table('business').' as b using(`business_account`) where o.`account`=\''.$_SESSION['account'].'\' and o.`order_sn`=\''.$order_sn.'\'';

    $order = $db->fetchRow($get_order);

    if( !empty($order['express_sn']) ) {
        $get_express_info = 'select `code`,`name` from ' . $db->table('express') . ' where `id`=' . $order['express_id'];
        $express_info = $db->fetchRow($get_express_info);

//        $express_flow = query_express($express_info['code'], $order['express_sn']);
//        $express_flow = json_decode($express_flow, true);
        assign('express_flow', $express_flow);
        assign('express_info', $express_info);
    }


    $get_order_detail = 'select `od`.`integral`,od.`product_attributes`,od.`product_price`,od.`product_name`,od.`product_sn`,p.`id`,p.`img`,od.`count` from '.$db->table('order_detail').' as od '.
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
    $template = 'order-detail.phtml';
    $_SESSION['order_sn'] = $order_sn;
}

if( 'paging' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    if(!check_cross_domain() && isset($_SESSION['account']) ) {
        $page = intval(getPOST('page'));
        $status = intval(getPOST('status'));

        $where = ' o.account = \''.$_SESSION['account'].'\'';
        if($status > 0 && $status < 8)
        {
            $where .= ' and o.`status`='.$status;
        }

        if($status > 0 && $status >= 8 && $status < 12)
        {
            $where .= ' and o.`status`>='.$status.' and o.`status` < 12';
        }

        if( $status == 12 ) {
            $where .= ' and o.`status`='.$status;
        }

        $get_total = 'select count(id) from '.$db->table('order').' as o where '.$where;
        $total = $db->fetchOne($get_total);
        $total_page = ceil( $total / $page_count );

        $page = $page > $total_page ? $total_page : $page;
        $page = $page < 1 ? 1 : $page;
        $offset = $page_count * ( $page - 1 );

        $get_order_list = 'select o.`order_sn`,b.`shop_name`,o.`status`,o.`amount`,o.`business_account`,o.`add_time`,b.`id` as bid from '.$db->table('order').' as o join '.
            $db->table('business').' as b using(`business_account`) where ';
        $get_order_list .= $where;

        $get_order_list .= ' order by o.`add_time` DESC limit '.$offset.','.$page_count;

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
        assign('page', $page);
        assign('total_page', $total_page);
        assign('status', $status);
        $response['content'] = $smarty->fetch('order-item.phtml');
        $response['error'] = 0;
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


if( $act == 'list' ) {
    $status = intval(getGET('status'));
    $status = $status < 0 || $status > 12 ? 0 : $status;

    $where = '';
    if($status > 0 && $status < 8)
    {
        $where .= ' and o.`status`='.$status;
    }

    if($status > 0 && $status >= 8 && $status < 12)
    {
        $where .= ' and o.`status`>='.$status.' and o.`status` < 12';
    }

    if( $status == 12 ) {
        $where .= ' and o.`status`='.$status;
    }

    $get_all_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\'';
    $all_total = $db->fetchOne($get_all_total);

    $get_pay_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 1';
    $pay_total = $db->fetchOne($get_pay_total);

    $get_receive_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 6';
    $receive_total = $db->fetchOne($get_receive_total);

    $get_comment_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 7';
    $comment_total = $db->fetchOne($get_comment_total);

    $get_refund_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status >= 8 and status < 12';
    $refund_total = $db->fetchOne($get_refund_total);

    $get_complete_total = 'select count(id) from '.$db->table('order').' as o where o.`account`=\''.$_SESSION['account'].'\' and status = 12';
    $complete_total = $db->fetchOne($get_complete_total);

    switch($status) {
        case 0:$total_page = ceil($all_total / $page_count);break;
        case 1:$total_page = ceil($pay_total / $page_count);break;
        case 4:$total_page = ceil($receive_total / $page_count);break;
        case 6:$total_page = ceil($comment_total / $page_count);break;
        case 8:$total_page = ceil($refund_total / $page_count);break;
        case 12:$total_page = ceil($complete_total / $page_count);break;
    }
//    echo $total_page;exit;
    $get_order_list = 'select o.`order_sn`,b.`shop_name`,o.`status`,o.`amount`,o.`business_account`,o.`add_time`,b.`id` as bid from '.$db->table('order').' as o join '.
        $db->table('business').' as b using(`business_account`) where o.`account`=\''.$_SESSION['account'].'\'';
    $get_order_list .= $where;

    $get_order_list .= ' order by o.`add_time` DESC limit 0,'.$page_count;



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
    assign('total_page', $total_page);
    assign('all_total', $all_total);
    assign('pay_total', $pay_total);
    assign('receive_total', $receive_total);
    assign('comment_total', $comment_total);
    assign('refund_total', $refund_total);
    assign('complete_total', $complete_total);
}

$smarty->display($template);