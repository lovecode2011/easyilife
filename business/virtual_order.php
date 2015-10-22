<?php
/**
 * 消费券管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-10-16
 * @version 1.0.0
 */

include 'library/init.inc.php';
business_base_init();

$template = 'virtual_order/';
assign('subTitle', '消费券管理');

$action = 'view|consume';
$operation = 'consume';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));

$status_str = array(
    0 => '有效',
    1 => '已使用',
    2 => '已过期',
    3 => '失效',
);

//===========================================================================
if( 'consume' == $opera ) {
    $response = array('error'=>1, 'msg'=>'', 'errmsg'=>array());
    if (!check_purview('pur_virtual_order_edit', $_SESSION['business_purview'])) {
        $response['msg'] = '权限不足';
        echo json_encode($response);
        exit;
    }
    $id = trim(getPOST('id'));
    if( 0 >= $id ) {
        $response['msg'] = '参数错误';
        echo json_encode($response);
        exit;
    }

    $get_content = 'select * from '.$db->table('order_content');
    $get_content .= ' where id = \''.$id.'\' and business_account = \''.$_SESSION['business_account'].'\'';
    $get_content .= ' limit 1';

    $content = $db->fetchRow($get_content);
    if( empty($content) ) {
        $response['msg'] = '消费券不存在';
        echo json_encode($response);
        exit;
    }

    if( 0 != $content['status'] ) {
        $response['msg'] = '消费券无法使用';
        echo json_encode($response);
        exit;
    }

    if( time() < $content['begin_time'] || time() > $content['end_time'] ) {
        $response['msg'] = '消费券超过有效期';
        echo json_encode($response);
        exit;
    }

    $mobile = trim(getPOST('mobile'));
    $code = trim(getPOST('code'));

    if( '' == $mobile || 11 != strlen($mobile) ) {
        $response['msg'] = '参数错误';
        $response['errmsg']['mobile'] = '-请输入手机号码';
    }

    if( '' == $code ) {
        $response['msg'] = '参数错误';
        $response['errmsg']['code'] = '-请输入消费码';
    }

    if( 0 != count($response['errmsg']) ) {
        echo json_encode($response);
        exit;
    }

    if( $content['mobile'] != $mobile ) {
        $response['msg'] = '手机号码错误';
        $response['errmsg']['mobile'] = '-手机号码错误';
    }

    if( $content['code'] != $code ) {
        $response['msg'] .= ($response['msg']) ? ',消费码错误' : '消费码错误';
        $response['errmsg']['code'] = '-消费码错误';
    }

    if( 0 != count($response['errmsg']) ) {
        echo json_encode($response);
        exit;
    }


    //存在一个实体产品则不改变订单状态
    $need_change_order_status = true;

    $get_detail_list = 'select * from '.$db->table('order_detail').' where order_sn = \''.$content['order'].'\'';
    $detail_list = $db->fetchAll($get_detail_list);

    if( $detail_list ) {
        foreach( $detail_list as $key => $detail ) {
            if( $detail['is_virtual'] == 0 ) {
                $need_change_order_status = false;
            }
        }
    }

    //同张订单的所有卡券都消费完则改变订单状态
    if( $need_change_order_status ) {
        $get_content_list = 'select * from '.$db->table('order_content').' where order_sn = \''.$content['order'].'\' and id <> '.$id;
        $content_list = $db->fetchAll($get_content_list);

        $all_use = true;
        if( $content_list ) {
            foreach( $content_list as $key => $value ) {
                if( $value['status'] == 0 ) {   //还存在另一张消费券未消费
                    $all_use = false;
                    break;
                }
            }
            $need_change_order_status = $all_use ? true : false;

        }
    }

    $data = array(
        'status' => 1,
    );

    $db->begin();
    $transaction = true;
    if( !$db->autoUpdate('order_content', $data, 'id = '.$id) ) {
        $transaction = false;
    }

    if( $need_change_order_status ) {
        $data = array(
            'status' => 12,
        );

        if ( !$db->autoUpdate('order', $data, 'order_sn = \''.$content['order_sn'].'\'') ) {
            $transaction = false;
        }
    }

    if( $transaction ) {
        $db->commit();
        $response['error'] = 0;
        $response['msg'] = '消费成功';
    } else {
        $db->rollback();
        $response['msg'] = '系统繁忙，请稍后重试';
    }

    echo json_encode($response);
    exit;
}

//===========================================================================

if( 'view' == $act ) {
    if (!check_purview('pur_virtual_order_view', $_SESSION['business_purview'])) {
        show_system_message('权限不足', array());
        exit;
    }

    $status = intval(getGET('status'));
    if( $status == 0 ) {
        assign('status', 0);
        assign('order_status', '');
        $and_where = '';
    } else {
        switch( $status ) {
            case 1: $and_where .= ' and a.status = 0';break;
            case 2: $and_where .= ' and a.status = 1';break;
            case 3: $and_where .= ' and a.status = 2';break;
            case 4: $and_where .= ' and a.status = 3';break;
            default: $and_where = '';break;
        }
        assign('status', $status);
        assign('order_status', $status_str[$status - 1]);
    }

    $mobile = trim(getGET('mobile'));
    if( $mobile ) {
        $mobile = $db->escape($mobile);
        $and_where .= ' and mobile like \'%'.$mobile.'%\'';
    }
    assign('mobile', $mobile);

    //分页参数
    $page = intval(getGET('page'));
    $count = intval(getGET('count'));
    //获取总数
    $get_total = 'select count(*) from '.$db->table('order_content').' as a';
    $get_total .= ' where a.business_account = \''.$_SESSION['business_account'].'\'';
    $get_total .= $and_where;
    $total = $db->fetchOne($get_total);

    $count = ( $count <= 0 ) ? 10 : $count;
    $total_page = ceil( $total / $count );
    $page = ( $page > $total_page ) ? $total : $page;
    $page = ( $page <= 0 ) ? 1 : $page;
    $offset = ( $page - 1 ) * $count;

    $get_content_list = 'select a.*, p.img from '.$db->table('order_content').' as a';
    $get_content_list .= ' left join '.$db->table('product').' as p on a.product_sn = p.product_sn';
    $get_content_list .= ' where a.business_account = \''.$_SESSION['business_account'].'\'';
    $get_content_list .= $and_where;
    $get_content_list .= ' order by id desc';
    $get_content_list .= ' limit '.$offset.','.$count;
    $content_list = $db->fetchAll($get_content_list);
//    echo $get_content_list;exit;

    if( $content_list ) {
        foreach( $content_list as $key => $content ) {
            $content_list[$key]['status_str'] = $status_str[$content['status']];
            $content_list[$key]['begin_time_str'] = $content['begin_time'] ? date('Y-m-d H:i:s', $content['begin_time']) : '';
            $content_list[$key]['end_time_str'] = $content['end_time'] ? date('Y-m-d H:i:s', $content['end_time']) : '';
        }
    }

    assign('content_list', $content_list);
    create_pager($page, $total_page, $total);

    assign('count', $count);
}


if( 'consume' == $act ) {
    if (!check_purview('pur_virtual_order_edit', $_SESSION['business_purview'])) {
        show_system_message('权限不足', array());
        exit;
    }
    $id = trim(getGET('id'));
    if( 0 >= $id ) {
        show_system_message('参数错误');
    }

    $get_content = 'select * from '.$db->table('order_content');
    $get_content .= ' where id = \''.$id.'\' and business_account = \''.$_SESSION['business_account'].'\'';
    $get_content .= ' limit 1';

    $content = $db->fetchRow($get_content);
    if( empty($content) ) {
        show_system_message('消费券不存在');
    }

    if( 0 != $content['status'] ) {
        show_system_message('消费券无法使用');
    }

    assign('content', $content);
}


$template .= $act.'.phtml';
$smarty->display($template);

