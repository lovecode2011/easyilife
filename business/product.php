<?php
/**
 * 商户产品管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-08-19
 * @version 1.0.0
 */

include 'library/init.inc.php';

//商户管理后台初始化
business_base_init();
$template = 'product/';

$action = 'view|add|edit|delete|cycle|revoke|remove';
$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;
//===============================================================================



//===============================================================================

if( 'view' == $act ) {
    if( !check_purview('pur_product_view', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }


}

if( 'add' == $act ) {
    if( !check_purview('pur_product_add', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
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

if( 'edit' == $act ) {
    if( !check_purview('pur_product_edit', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'delete' == $act ) {
    if( !check_purview('pur_product_delete', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'cycle' == $act ) {
    if( !check_purview('pur_product_delete', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'revoke' == $act ) {
    if( !check_purview('pur_product_delete', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'remove' == $act ) {
    if( !check_purview('pur_product_delete', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}


$template .= $act.'.phtml';
$smarty->display($template);

