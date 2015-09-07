<?php
/**
 * 产品管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-09-06
 * @version 1.0.0
 */

include 'library/init.inc.php';
back_base_init();

$template = 'product/';
assign('subTitle', '产品管理');

$action = 'view|exam';
$operation = '';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));
//===========================================================================

if( 'add' == $opera ) {
    if( !check_purview('pur_industry_add', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'edit' == $opera ) {
    if( !check_purview('pur_industry_edit', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}


//===========================================================================

if( 'view' == $act ) {
    if( !check_purview('pur_industry_view', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $where = '';

    $status = intval(getGET('status'));
    if( $status != 0 ) {
        switch($status) {
            case 1:
                $where .= ' where status = 2';
                break;
            case 2:
                $where .= ' where status = 3';
                break;
            case 3:
                $where .= ' where status = 4';
                break;
            default:
                $where .= ' where status = 0';
                break;
        }
        assign('status', $status);
    } else {
        $where .= ' where status > 1 and status < 5';
    }

    $keyword = trim(getGET('keyword'));
    if( '' != $keyword ) {
        $keyword = $db->escape($keyword);
        $where .= ' and a.name like \'%'.$keyword.'%\' || product_sn like \'%'.$keyword.'%\'';
    }
    $count = intval(getGET('count'));
    $count_array = array(10, 25, 50 , 100);
    $count = ( in_array($count, $count_array) ) ? $count : 10;

    $get_total = 'select count(*) from '.$db->table('product');
    $get_total .= $where;
    $total = $db->fetchOne($get_total);
    $total_page = ceil( $total / $count );

    $page = intval(getGET('page'));
    $page = ( $page > $total_page ) ? $total_page : $page;
    $page = ( 0 >= $page ) ? 1 : $page;

    create_pager($page, $total_page, $total);
    assign('count', $count);
    assign('keyword', $keyword);

    $offset = ($page - 1) * $count;

    $get_product_list = 'select a.*, b.name as category_name from '.$db->table('product').' as a';
    $get_product_list .= ' left join '.$db->table('category').' as b on a.category_id = b.id';
    $get_product_list .= $where;
    $get_product_list .= ' order by order_view asc, id desc';
    $get_product_list .= ' limit '.$offset.','.$count;
    $product_list = $db->fetchAll($get_product_list);

    $status_array = array(
        1 => '待发布',
        2 => '待审核',
        3 => '已下架',
        4 => '已上架',
    );

    if( $product_list ) {
        foreach( $product_list as $key => $product ) {
            if( file_exists(realpath('..'.$product['img']))) {
                $product[$key]['img'] = '..'.$product['img'];
            }
            $product_list[$key]['status_str'] = $status_array[$product['status']];
            $product_list[$key]['exam_str'] = ( $product['status'] == 2 ) ? '审核' : '';
        }
    }
    assign('product_list', $product_list);
}

if( 'add' == $act ) {
    if( !check_purview('pur_industry_add', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'edit' == $act ) {
    if( !check_purview('pur_industry_edit', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'delete' == $act ) {
    if( !check_purview('pur_industry_del', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}


$template .= $act.'.phtml';
$smarty->display($template);

