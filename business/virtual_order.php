<?php
/**
 *  虚拟产品订单
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-10-14
 * @version 1.0.0
 */

include 'library/init.inc.php';

//商户管理后台初始化
business_base_init();
$template = 'virtual_order/';

$action = 'view|refund|detail|export';
$act = check_action($action, getGET('act'));
$operation = '';
$opera = check_action($operation, getPOST('opera'));
$act = ( $act == '' ) ? 'view' : $act;

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


//=====================================================================================


//=====================================================================================

if( 'view' == $act ) {
    if( !check_purview('pur_virtual_order_view', $_SESSION['business_purview']) ) {
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
    $get_total .= ' where business_account = \''.$_SESSION['business_account'].'\'';
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
    $get_order_list .= ' where a.business_account = \''.$_SESSION['business_account'].'\'';
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


if( 'refund' == $act ) {
    if( !check_purview('pur_virtual_order_edit', $_SESSION['business_purview']) ) {
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
    $get_order .= ' and is_virtual = 1';    //虚拟订单
    $get_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    $order = $db->fetchRow($get_order);

    if( empty($order) ) {
        show_system_message('订单不存在', array());
        exit;
    }
    if( 7 != $order['status'] ) {
        show_system_message('参数错误', array());
        exit;
    }
    $update_order = 'update '.$db->table('order').' set';
    $update_order .= ' status = 8 ';
    $update_order .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $update_order .= ' and order_sn = \''.$order_sn.'\' limit 1';

    if( $db->update($update_order) ) {
        $log_data = array(
            'order_sn' => $order_sn,
            'operator' => $_SESSION['business_admin'],
            'status' => 8,
            'add_time' => time(),
            'remark' => '退单成功'
        );
        $db->autoInsert('order_log', array($log_data));

        $links = array(
            array('alt' => '已退单订单列表', 'link' => 'virtual_order.php?status=8'),
        );
        show_system_message('退单已完成', $links);
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }
}

if( 'detail' == $act ) {
    if( !check_purview('pur_virtual_order_view', $_SESSION['business_purview']) ) {
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

    $get_order .= ' where a.`business_account` = \''.$_SESSION['business_account'].'\'';
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

if( 'export' == $act ) {
    if( !check_purview('pur_virtual_order_view', $_SESSION['business_purview']) ) {
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

    $get_order_list = 'select a.`order_sn`, a.`add_time`, a.`receive_time`, a.`status`, a.`amount`, a.`integral_amount`, a.`business_account`, a.`product_amount`,';
    $get_order_list .= ' a.`integral_given_amount`, a.`payment_name`, a.`payment_sn`, a.`start_time`, a.`end_time`,a.`consignee`,a.`mobile`, a.`address`,';
    $get_order_list .= ' p.`province_name`, city.`city_name`, d.`district_name`, g.`group_name`,product.`img`, a.`product_name`, a.`product_sn`';
    $get_order_list .= ' from '.$db->table('order').' as a';
    $get_order_list .= ' left join '.$db->table('province').' as p on a.province = p.id';
    $get_order_list .= ' left join '.$db->table('city').' as city on a.city = city.id';
    $get_order_list .= ' left join '.$db->table('district').' as d on a.district = d.id';
    $get_order_list .= ' left join '.$db->table('group').' as g on a.group = g.id';
    $get_order_list .= ' left join '.$db->table('product').' as product on a.product_sn = product.product_sn';
    $get_order_list .= ' where a.business_account = \''.$_SESSION['business_account'].'\'';
    $get_order_list .= $and_where;
    $get_order_list .= ' and a.is_virtual = 1';    //虚拟产品
    $get_order_list .= ' order by a.add_time desc';
    $order_list = $db->fetchAll($get_order_list);
//    echo $get_order_list;exit;

    if( $order_list ) {
        foreach( $order_list as $key => $order ) {
            $order_list[$key]['reward_amount'] = floatval($order['reward_amount']) ? $order['reward_amount'] : '0.00';
            $order_list[$key]['add_time_str'] = date('Y-m-d H:i:s', $order['add_time']);
            $order_list[$key]['pay_time_str'] = intval($order['pay_time']) ? date('Y-m-d H:i:s', $order['pay_time']) : '未支付';
            $order_list[$key]['receive_time_str'] = $order['receive_time'] ? date('Y-m-d H:i:s', $order['receive_time']) : '未消费';
            $order_list[$key]['status_str'] = $status_str[$order['status']];
            $order_list[$key]['start_time_str'] = $order['start_time'] ? date('Y-m-d H:i:s', $order['start_time']) : '';
            $order_list[$key]['end_time_str'] = $order['end_time'] ? date('Y-m-d H:i:s', $order['end_time']) : '';
            $order_list[$key]['expired'] = ($order['start_time'] && $order['end_time']) ? date('Y-m-d H:i:s', $order['start_time']) .'~'. date('Y-m-d H:i:s', $order['end_time']) : '永久有效';
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

    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(25);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(25);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(25);
    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);


    $i = 1;
    //设置填充的样式和背景色
    $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->getStartColor()->setRGB('ff0000');
    $i++;
    foreach( $order_list as $key => $order ) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '订单编号')
            ->setCellValue('B'.$i, $order['order_sn'])
            ->setCellValue('C'.$i, '订单状态')
            ->setCellValue('D'.$i, $order['status_str'])
            ->setCellValue('E'.$i, '下单时间')
            ->setCellValue('F'.$i, $order['add_time_str']);

        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '支付时间')
            ->setCellValue('B'.$i, $order['pay_time_str'])
            ->setCellValue('C'.$i, '支付方式')
            ->setCellValue('D'.$i, $order['payment_name'])
            ->setCellValue('E'.$i, '消费时间')
            ->setCellValue('F'.$i, $order['receive_time_str']);

        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '返利')
            ->setCellValue('B'.$i, $order['reward_amount'].' ')
            ->setCellValue('C'.$i, '购买积分')
            ->setCellValue('D'.$i, $order['integral_amount'].' ')
            ->setCellValue('E'.$i, '赠送积分')
            ->setCellValue('F'.$i, $order['integral_given_amount'].' ');

        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '有效期')
            ->setCellValue('B'.$i, $order['expired']);

        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '产品信息');

        $i++;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '产品编号')
            ->setCellValue('B'.$i, $order['product_sn'])
            ->setCellValue('C'.$i, '产品名称')
            ->setCellValue('D'.$i, $order['product_name']);

        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
        $objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
        $objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
        //设置填充的样式和背景色
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->getStartColor()->setARGB('FF808080');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '消费内容')
            ->setCellValue('C'.$i, '数量/规格')
            ->setCellValue('E'.$i, '小计');

        $get_content_list = 'select * from '.$db->table('order_content').' where order_sn = \''.$order['order_sn'].'\'';
        $content_list = $db->fetchAll($get_content_list);

        if( $content_list ) {
            $i++;
            foreach( $content_list as $content ) {
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
                $objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
                $objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $content['content'])
                    ->setCellValue('C'.$i, $content['count'])
                    ->setCellValue('E'.$i, $content['total']);
                $i++;
            }
        }
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
        $objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '合计')
            ->setCellValue('E'.$i, $order['amount'].' ');

        $i++;
        //设置填充的样式和背景色
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->getStartColor()->setARGB('FF808080');

        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '地址')
            ->setCellValue('B'.$i, $order['address']);

        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '联系人')
            ->setCellValue('B'.$i, $order['consignee']);

        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '联系电话')
            ->setCellValue('B'.$i, $order['mobile'].' ');

        $i++;
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '备注')
            ->setCellValue('B'.$i, $order['remark']);

        $i++;
        //设置填充的样式和背景色
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle( 'A'.$i.':F'.$i)->getFill()->getStartColor()->setRGB('ff0000');
        $i++;
    }

    $objPHPExcel->getActiveSheet()->setTitle('虚拟订单');
//    $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(40);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="虚拟订单'.date('Ymd', time()).'.xlsx"');
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


$template .= $act.'.phtml';
$smarty->display($template);

