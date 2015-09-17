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

$action = 'view|exam|reject';
$operation = 'exam|reject';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));
//===========================================================================

if( 'exam' == $opera ) {
    if( !check_purview('pur_product_exam', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $id = intval(getPOST('id'));
    if( 0 >= $id ) {
        show_system_message('参数错误', array());
        exit;
    }

    $get_product = 'select * from '.$db->table('product').' where id = \''.$id.'\' and status = 2 limit 1';
    $product = $db->fetchRow($get_product);
    if( empty($product) ) {
        show_system_message('产品不存在', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }

    if( $product['status'] == 5 ) {
        show_system_message('产品已被删除', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }

    $update_product = 'update '.$db->table('product').' set ';
    $update_product .= ' status = 3';
    $update_product .= ' ,prev_status = 2';
    $update_product .= ' where id = \''.$id.'\' limit 1';

    if( $db->update($update_product) ) {

        //系统通知商户===

        //=============
        show_system_message('产品通过审核', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }
}

if( 'reject' == $opera ) {
    if( !check_purview('pur_product_exam', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $id = intval(getPOST('id'));
    if( 0 >= $id ) {
        show_system_message('参数错误', array());
        exit;
    }
    $message = trim(getPOST('message'));
    if( '' == $message ) {
        show_system_message('请输入驳回理由', array());
        exit;
    }
    $message = $db->escape($message);

    $get_product = 'select * from '.$db->table('product').' where id = \''.$id.'\' and status = 2 limit 1';
    $product = $db->fetchRow($get_product);
    if( empty($product) ) {
        show_system_message('产品不存在', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }

    if( $product['status'] == 5 ) {
        show_system_message('产品已被删除', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }

    $update_product = 'update '.$db->table('product').' set ';
    $update_product .= ' status = 1';
    $update_product .= ' ,prev_status = 2';
    $update_product .= ' where id = \''.$id.'\' limit 1';

    if( $db->update($update_product) ) {

        //系统通知商户===

        //=============
        show_system_message('产品审核已驳回', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }
}


//===========================================================================

if( 'view' == $act ) {
    if( !check_purview('pur_product_view', $_SESSION['purview']) ) {
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

if( 'exam' == $act ) {
    if( !check_purview('pur_product_exam', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $product_sn = trim(getGET('sn'));
    if( '' == $product_sn ) {
        show_system_message('参数错误', array());
        exit;
    }
    $product_sn = $db->escape($product_sn);

    $get_product = 'select a.*, b.attributes, b.inventory from '.$db->table('product').' as a';
    $get_product .= ' and a.product_sn = \''.$product_sn.'\' and status = 2 limit 1';
    $product = $db->fetchRow($get_product);
    if( !$product ) {
        show_system_message('产品不存在', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }
    if( $product['status'] == 5 ) {
        show_system_message('产品已被删除', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }

    $product['promote_begin'] = ( $product['promote_begin'] ) ? date('Y-m-d H:i:s', $product['promote_begin']) : '';
    $product['promote_end'] = ( $product['promote_end'] ) ? date('Y-m-d H:i:s', $product['promote_end']) : '';
    $product['img_src'] = (file_exists('..'.$product['img'])) ? '..'.$product['img'] : $product['img'];

    assign('product', $product);
    assign('attributes', $product['attributes']);

    $get_category_list = 'select * from '.$db->table('category');
    $get_category_list .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_category_list .= ' order by `path` ASC';
    $category_list = $db->fetchAll($get_category_list);
    if( $category_list ) {
        foreach ($category_list as $key => $category) {
            $count = count(explode(',', $category['path']));
            if ($count > 2) {
                $temp = '|--' . $category['name'];
                $count = 1;
                while ($count--) {
                    $temp = '&nbsp;&nbsp;' . $temp;
                }

                $category['name'] = $temp;
                $category_list[$key] = $category;
            }
        }
    }
    assign('category_list', $category_list);

    $get_product_type_list = 'select  * from '.$db->table('product_type').' where 1 order by id asc';
    $product_type_list = $db->fetchAll($get_product_type_list);
    assign('product_type_list', $product_type_list);

    $get_product_attr_list = 'select * from '.$db->table('product_attributes').' where 1 order by product_type_id asc, id asc';
    $product_attr_list = $db->fetchAll($get_product_attr_list);

    $target_attr_list = array();
    $length = count($product_attr_list);
    for( $i = 0; $i < $length; ) {
        $pid = $product_attr_list[$i]['product_type_id'];
        $temp = array();
        do {
            $temp[] = $product_attr_list[$i];
            $i++;
        } while( $i < $length && $pid == $product_attr_list[$i]['product_type_id'] );
        $target_attr_list[$pid] = $temp;
    }
    assign('json_attr_list', json_encode($target_attr_list));


    $get_brand_list = 'select * from '.$db->table('brand').' where 1 order by id asc';
    $brand_list = $db->fetchAll($get_brand_list);
    assign('brand_list', $brand_list);
}

if( 'reject' == $act ) {
    if( !check_purview('pur_product_exam', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
    $id = intval(getGET('id'));
    if( 0 >= $id ) {
        show_system_message('参数错误', array());
        exit;
    }

    $get_product = 'select * from '.$db->table('product').' where id = \''.$id.'\' and status = 2 limit 1';
    $product = $db->fetchRow($get_product);
    if( empty($product) ) {
        show_system_message('产品不存在', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }

    if( $product['status'] == 5 ) {
        show_system_message('产品已被删除', array(array('link' => 'product.php', 'alt' => '产品管理')));
        exit;
    }
    assign('id', $id);
}


$template .= $act.'.phtml';
$smarty->display($template);
