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

$action = 'view|deliver|prepare|agree|refund|delete|detail|export|pay';
$operation = 'deliver';

$act = check_action($action, getGET('act'));
$opera = check_action($operation, getPOST('opera'));
$act = ( $act == '' ) ? 'view' : $act;
//===============================================================================
if('express_info' == $opera)
{
    $order_sn = getPOST('order_sn');

    $response = array('error'=>1, 'msg'=>'');

    if($order_sn == '')
    {
        $response['msg'] = '参数错误';
    } else {
        $order_sn = $db->escape($order_sn);
    }

    $get_order_info = 'select `express_id`,`status`,`express_sn` from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\'';
    $order = $db->fetchRow($get_order_info);

    if($order && $order['status'] == 6)
    {
        $get_express_code = 'select `code` from '.$db->table('express').' where `id`='.$order['express_id'];
        $express_info = query_express($db->fetchOne($get_express_code   ), $order['express_sn']);
        $express_info = json_decode($express_info, true);
        assign('order_info', $express_info);
        $response['error'] = 0;
        $response['msg'] = $smarty->fetch('public/express_info.phtml');
    } else {
        $response['msg'] = '当前没有任何信息';
    }

    echo json_encode($response);
    exit;
}

if( 'deliver' == $opera ) {
    if( !check_purview('pur_order_edit', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $express_sn = trim(getPOST('express_sn'));
    $express_id = intval(getPOST('express_id'));
    $order_sn = trim(getPOST('sn'));

    if($express_id <= 0)
    {
        show_system_message('请选择快递公司', array());
        exit;
    }

    if( '' == $express_sn ) {
        show_system_message('快递单号不能为空', array());
        exit;
    }
    $express_sn = $db->escape($express_sn);

    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select * from '.$db->table('order');
    $get_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_order .= ' and is_virtual = 0';    //实体订单
    $get_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }

    if( $order['status'] != 5 ) {
        show_system_message('参数错误', array());
        exit;
    }

    $update_order = 'update '.$db->table('order').' set';
    $update_order .= ' `status` = 6';
    $update_order .= ', `express_id`='.$express_id;
    $update_order .= ', `express_sn` = \''.$express_sn.'\'';
    $update_order .= ', `delivery_time` = '.time();
    $update_order .= ' where order_sn = \''.$order_sn.'\' limit 1';
    if( $db->update($update_order) ) {

        $log_data = array(
            'order_sn' => $order_sn,
            'operator' => $_SESSION['business_admin'],
            'status' => 6,
            'add_time' => time(),
            'remark' => '发货'
        );
        $db->autoInsert('order_log', array($log_data));
        //扣减库存
        $get_order_detail = 'select `product_sn`,`count`,`attributes` from '.$db->table('order_detail').' where `order_sn`=\''.$order_sn.'\'';
        $order_detail = $db->fetchAll($get_order_detail);
        foreach($order_detail as $od)
        {
            consume_inventory($od['product_sn'], $od['attributes'], $od['count'], 1);
        }

        $links = array(
            array('alt' => '已发货订单列表', 'link' => 'order.php?status=6'),
        );
        show_system_message('已发货', $links);
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}


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
        12 => '已完成',
    );

    $status = intval(getGET('status'));
    if( 0 >= $status || 12 < $status || 3 == $status || 2 == $status ) {
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
    $get_total = 'select count(*) from '.$db->table('order');
    $get_total .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_total .= $and_where;
    $get_total .= ' and is_virtual = 0';    //实体产品
    $total = $db->fetchOne($get_total);

    $page = ( $page > $total ) ? $total : $page;
    $page = ( $page <= 0 ) ? 1 : $page;
    $count = ( $count <= 0 ) ? 10 : $count;
    $offset = ( $page - 1 ) * $count;
    $total_page = ceil( $total / $count );

    $get_order_list = 'select a.*, p.province_name, city.city_name, d.district_name, g.group_name from '.$db->table('order').' as a';
    $get_order_list .= ' left join '.$db->table('province').' as p on a.province = p.id';
    $get_order_list .= ' left join '.$db->table('city').' as city on a.city = city.id';
    $get_order_list .= ' left join '.$db->table('district').' as d on a.district = d.id';
    $get_order_list .= ' left join '.$db->table('group').' as g on a.group = g.id';

    $get_order_list .= ' where `business_account` = \''.$_SESSION['business_account'].'\'';
    $get_order_list .= $and_where;
    $get_order_list .= ' and a.is_virtual = 0';
    $get_order_list .= ' order by add_time desc';
    $get_order_list .= ' limit '.$offset.','.$count;
    $order_list = $db->fetchAll($get_order_list);

//echo $get_order_list;exit;
    if( $order_list ) {
        foreach ($order_list as $key => $order) {
            $order_list[$key]['add_time_str'] = $order['add_time'] ? date('Y-m-d H:i:s', $order['add_time']) : '';
            $order_list[$key]['delivery_time_str'] = $order['delivery_time'] ? date('Y-m-d H:i:s', $order['delivery_time']) : '未发货';
            $order_list[$key]['receive_time_str'] = $order['receive_time'] ? date('Y-m-d H:i:s', $order['receive_time']) : '未收货';
            $order_list[$key]['pay_time_str'] = $order['pay_time'] ? date('Y-m-d H:i:s', $order['pay_time']) : '未支付';
            $order_list[$key]['status_str'] = $status_str[$order['status']];
        }
    }

    assign('order_list', $order_list);
    create_pager($page, $total_page, $total);

    assign('count', $count);
    assign('st', $st);
    assign('et', $et);
}

//配货
if( 'prepare' == $act ) {
    if( !check_purview('pur_order_edit', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $order_sn = trim(getGET('sn'));
    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select * from '.$db->table('order');
    $get_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_order .= ' and is_virtual = 0';    //实体订单
    $get_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }

    if( $order['status'] != 4 ) {
        show_system_message('参数错误', array());
        exit;
    }

    $update_order = 'update '.$db->table('order').' set';
    $update_order .= ' `status` = 5';
    $update_order .= ' where order_sn = \''.$order_sn.'\' limit 1';
    if( $db->update($update_order) ) {

        $log_data = array(
            'order_sn' => $order_sn,
            'operator' => $_SESSION['business_admin'],
            'status' => 5,
            'add_time' => time(),
            'remark' => '配货'
        );
        $db->autoInsert('order_log', array($log_data));

        $links = array(
            array('alt' => '配货中订单列表', 'link' => 'order.php?status=5'),
        );
        show_system_message('请前往配货，别让顾客久等', $links);
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}

//发货
if( 'deliver' == $act ) {
    if( !check_purview('pur_order_edit', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $order_sn = trim(getGET('sn'));
    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select * from '.$db->table('order');
    $get_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_order .= ' and is_virtual = 0';    //实体订单
    $get_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }

    if( $order['status'] != 5 ) {
        show_system_message('参数错误', array());
        exit;
    }

    $get_express_list = 'select `id`,`name` from '.$db->table('express');

    assign('express_list', $db->fetchAll($get_express_list));
    assign('order', $order);
}

//同意退单
if( 'agree' == $act ) {
    if( !check_purview('pur_order_edit', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $order_sn = trim(getGET('sn'));
    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select * from '.$db->table('order');
    $get_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_order .= ' and is_virtual = 0';    //实体订单
    $get_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }

    $update_order = 'update '.$db->table('order').' set';
    $update_order .= ' status = 9';
    $update_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $update_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    if( $db->update($update_order) ) {
        $log_data = array(
            'order_sn' => $order_sn,
            'operator' => $_SESSION['business_admin'],
            'status' => 9,
            'add_time' => time(),
            'remark' => '同意申请退单'
        );
        $db->autoInsert('order_log', array($log_data));

        $links = array(
            array('alt' => '退单中订单列表', 'link' => 'order.php?status=9'),
        );
        show_system_message('您已同意退单', $links);
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}

//退单成功
if( 'refund' == $act ) {
    if( !check_purview('pur_order_edit', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $order_sn = trim(getGET('sn'));
    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select * from '.$db->table('order');
    $get_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_order .= ' and is_virtual = 0';    //实体订单
    $get_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }
    if( 9 != $order['status'] ) {
        show_system_message('参数错误', array());
        exit;
    }
    $update_order = 'update '.$db->table('order').' set';
    $update_order .= ' status = 10 ';
    $update_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $update_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    if( $db->update($update_order) ) {
        $log_data = array(
            'order_sn' => $order_sn,
            'operator' => $_SESSION['business_admin'],
            'status' => 10,
            'add_time' => time(),
            'remark' => '退单成功'
        );
        $db->autoInsert('order_log', array($log_data));

        $links = array(
            array('alt' => '已退单订单列表', 'link' => 'order.php?status=10'),
        );
        show_system_message('退单已完成', $links);
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }

}

if( 'detail' == $act ) {
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
        12 => '已完成',
    );


    $order_sn = trim(getGET('sn'));

    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
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
    $get_order .= ' and a.is_virtual = 0';    //实体订单
    $get_order .= ' and order_sn = \''.$order_sn.'\'';
    $get_order .= ' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
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

    assign('order', $order);
    assign('order_detail', $order_detail);
}
//导出数据
if( 'export' == $act ) {
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
        12 => '已完成',
    );

    $status = intval(getGET('status'));
    if( 0 >= $status || 12 < $status || 3 == $status || 2 == $status ) {
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
    $get_total = 'select count(*) from '.$db->table('order');
    $get_total .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_total .= $and_where;
    $get_total .= ' and is_virtual = 0';    //实体产品
    $total = $db->fetchOne($get_total);

    $page = ( $page > $total ) ? $total : $page;
    $page = ( $page <= 0 ) ? 1 : $page;
    $count = ( $count <= 0 ) ? 10 : $count;
    $offset = ( $page - 1 ) * $count;
    $total_page = ceil( $total / $count );

    $get_order_list = 'select a.*, p.province_name, city.city_name, d.district_name, g.group_name from '.$db->table('order').' as a';
    $get_order_list .= ' left join '.$db->table('province').' as p on a.province = p.id';
    $get_order_list .= ' left join '.$db->table('city').' as city on a.city = city.id';
    $get_order_list .= ' left join '.$db->table('district').' as d on a.district = d.id';
    $get_order_list .= ' left join '.$db->table('group').' as g on a.group = g.id';

    $get_order_list .= ' where `business_account` = \''.$_SESSION['business_account'].'\'';
    $get_order_list .= $and_where;
    $get_order_list .= ' and a.is_virtual = 0';
    $get_order_list .= ' order by add_time desc';
    $order_list = $db->fetchAll($get_order_list);

//echo $get_order_list;exit;
    if( $order_list ) {
        foreach ($order_list as $key => $order) {
            $order_list[$key]['add_time_str'] = $order['add_time'] ? date('Y-m-d H:i:s', $order['add_time']) : '';
            $order_list[$key]['delivery_time_str'] = $order['delivery_time'] ? date('Y-m-d H:i:s', $order['delivery_time']) : '未发货';
            $order_list[$key]['receive_time_str'] = $order['receive_time'] ? date('Y-m-d H:i:s', $order['receive_time']) : '未收货';
            $order_list[$key]['pay_time_str'] = $order['pay_time'] ? date('Y-m-d H:i:s', $order['pay_time']) : '未支付';
            $order_list[$key]['status_str'] = $status_str[$order['status']];
        }
    }
    //导出
    if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');

    /** Include PHPExcel */
    require_once ROOT_PATH.'/plugins/PHPExcel/Classes/PHPExcel.php';


    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator($_SESSION['business_admin'])
        ->setLastModifiedBy($_SESSION['business_admin'])
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("订单")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("订单");


    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);

    // add data
    $i = 1;
    //设置填充的样式和背景色
    $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':H'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':H'.$i)->getFill()->getStartColor()->setRGB('ff0000');
    $i++;
    foreach( $order_list as $key => $order ) {
        $temp = $order['province_name'].$order['city_name'].$order['district_name'].$order['group_name'].'    ';
        $temp .= $order['address'].'    ';
        $order['self_delivery_str'] = $order['self_delivery'] == 0 ? '否' : '是';
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '订单编号')
            ->setCellValue('B'.$i, $order['order_sn'])
            ->setCellValue('C'.$i, '下单时间')
            ->setCellValue('D'.$i, $order['add_time_str'])
            ->setCellValue('E'.$i, '订单状态')
            ->setCellValue('F'.$i, $order['status_str'])
            ->setCellValue('G'.$i, '自提')
            ->setCellValue('H'.$i, $order['self_delivery_str']);
        if( 0 == $order['self_delivery'] ) {
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '收货人')
                ->setCellValue('B' . $i, $order['consignee'])
                ->setCellValue('C' . $i, '联系电话')
                ->setCellValue('D' . $i, $order['mobile'])
                ->setCellValue('E' . $i, '邮编')
                ->setCellValue('F' . $i, $order['zipcode']);
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '收货地址');
            $objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':F' . $i);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, $temp);
        }
        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells( 'A'.$i.':G'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '订单详情');
        $i++;
        //设置填充的样式和背景色
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':G'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':G'.$i)->getFill()->getStartColor()->setARGB('FF808080');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '产品编号')
            ->setCellValue('B'.$i, '产品名称')
            ->setCellValue('C'.$i, '返利')
            ->setCellValue('D'.$i, '产品积分')
            ->setCellValue('E'.$i, '赠送积分')
            ->setCellValue('F'.$i, '产品价格')
            ->setCellValue('G'.$i, '购买数量');
        $get_order_detail = 'select * from '.$db->table('order_detail');
        $get_order_detail .= ' where order_sn = \''.$order['order_sn'].'\' order by id asc';
        $order_detail = $db->fetchAll($get_order_detail);
        $i++;
        if( $order_detail ) {
            foreach ($order_detail as $detail) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $detail['product_sn'])
                    ->setCellValue('B' . $i, $detail['product_name'])
                    ->setCellValue('C' . $i, $detail['reward'])
                    ->setCellValue('D' . $i, $detail['integral'])
                    ->setCellValue('E' . $i, $detail['integral_given'])
                    ->setCellValue('F' . $i, $detail['product_price'])
                    ->setCellValue('G' . $i, $detail['count']);
                $i++;
            }
        }

        $objPHPExcel->getActiveSheet()->mergeCells( 'A'.$i.':B'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '合计')
            ->setCellValue('C'.$i, $order['reward_amount'])
            ->setCellValue('D'.$i, $order['integral_amount'])
            ->setCellValue('E'.$i, $order['integral_given_amount'])
            ->setCellValue('F'.$i, $order['amount']);

        $i++;
        //设置填充的样式和背景色
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':G'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':G'.$i)->getFill()->getStartColor()->setARGB('FF808080');

        if( $order['express_sn'] ) {
            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $i . ':F' . $i);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '物流信息');
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '物流公司')
                ->setCellValue('B' . $i, $order['delivery_name'])
                ->setCellValue('C' . $i, '发货单号')
                ->setCellValue('D' . $i, $order['express_sn'])
                ->setCellValue('E' . $i, '发货时间')
                ->setCellValue('F' . $i, $order['delivery_time_str']);
        }
        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':F' . $i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, '备注')
            ->setCellValue('B'.$i, $order['remark']);

        $i++;
        //设置填充的样式和背景色
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':H'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':H'.$i)->getFill()->getStartColor()->setRGB('ff0000');
        $i++;

    }
    $objPHPExcel->getActiveSheet()->setTitle('订单');
//    $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(40);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="订单'.date('Ymd', time()).'.xlsx"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}

//付款
if( 'pay' == $act ) {
    if( !check_purview('pur_order_edit', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $order_sn = trim(getGET('sn'));
    if( '' == $order_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $order_sn = $db->escape($order_sn);

    $get_order = 'select * from '.$db->table('order');
    $get_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_order .= ' and is_virtual = 0';    //实体订单
    $get_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }

    $update_order = 'update '.$db->table('order').' set';
    $update_order .= ' status = 4';
    $update_order .= ', pay_time = '.time();
    $update_order .= ', payment_name = \'线下支付\'';
    $update_order .= ', payment_id = 0';
    $update_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $update_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    if( $db->update($update_order) ) {
        $log_data = array(
            'order_sn' => $order_sn,
            'operator' => $_SESSION['business_admin'],
            'status' => 4,
            'add_time' => time(),
            'remark' => '确认已付款'
        );
        $db->autoInsert('order_log', array($log_data));

        $links = array(
            array('alt' => '待发货订单列表', 'link' => 'order.php?status=4'),
        );
        show_system_message('确认客户已付款', $links);
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}


$template .= $act.'.phtml';
$smarty->display($template);
