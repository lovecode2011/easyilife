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
$operation = 'add|edit';
$act = check_action($action, getGET('act'));
$opera = check_action($operation, getPOST('opera'));
$act = ( $act == '' ) ? 'view' : $act;
//===============================================================================

if( 'add' == $opera ) {
    if( !check_purview('pur_product_add', $_SESSION['business_purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }

    $product_sn = trim(getPOST('product_sn'));
    $status = intval(getPOST('status'));
    $name = trim(getPOST('name'));
    $category = intval(getPOST('category'));
    $product_type = intval(getPOST('type'));
    $brand = intval(getPOST('brand'));
    $price = floatval(getPOST('price'));
    $shop_price = floatval(getPOST('shop_price'));
    $lowest_price = floatval(getPOST('lowest_price'));
    $reward = floatval(getPOST('reward'));

    $integral = 0;
    $given_integral = 0;

    $img = trim(getPOST('img'));
    $desc = trim(getPOST('desc'));
    $detail = trim(getPOST('detail'));
    $promote_price = floatval(getPOST('promote_price'));
    $promote_begin = trim(getPOST('promote_begin'));
    $promote_end = trim(getPOST('promote_end'));
    $weight = intval(getPOST('weight'));
    $order_view = intval(getPOST('order_view'));
    $free_delivering = intval(getPOST('free_delivering'));


    $inventory = intval(getPOST('inventory'));
    $attr = getPOST('attr');

    if( '' == $product_sn ) {
        show_system_message('产品编号不能为空', array());
        exit;
    }
    $product_sn = $db->escape($product_sn);

    $status = ( $status == 1 ) ? 1 : 0;

    if( '' == $name ) {
        show_system_message('产品名称不能为空', array());
        exit;
    }
    $name = $db->escape($name);

    if( 0 >= $category ) {
        show_system_message('产品分类参数错误', array());
        exit;
    }

    if( 0 >= $product_type ) {
        show_system_message('产品类型参数错误', array());
        exit;
    }

    if( 0 >= $brand ) {
        show_system_message('品牌参数错误', array());
        exit;
    }

    if( 0 > $inventory ) {
        $inventory = 0;
    }

    if( 0 > $price ) {
        show_system_message('售价不能为负数', array());
        exit;
    }

    if( 0 > $shop_price ) {
        show_system_message('市场价不能为负数', array());
        exit;
    }

    if( 0 > $lowest_price ) {
        show_system_message('最低价不能为负数', array());
        exit;
    }

    if( 0 >= $reward ) {
        show_system_message('返利不能为负数', array());
        exit;
    }


    if( '' == $img ) {
        show_system_message('请选择一张图片作为封面', array());
        exit;
    }
    $img = $db->escape($img);

    $desc = $db->escape($desc);

    $detail = $db->escape($detail);

    $promote_price = ( $promote_price <= 0 ) ? 0 : $promote_price;

    if( '' != $promote_begin ) {
        if(preg_match('^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)\ \d{1,2}:\d{1,2}:\d{1,2}$', $publishTime)) {
            $dateTime = explode(' ', $promote_begin);
            $date = explode('-', $dateTime[0]);
            $time = explode(':', $dateTime[1]);

            $promote_begin = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
        } else {
            show_system_message('时间格式不正确', array());
            exit;
        }
    } else {
        $promote_begin = 0;
    }

    if( '' != $promote_end ) {
        if(preg_match('^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)\ \d{1,2}:\d{1,2}:\d{1,2}$', $publishTime)) {
            $dateTime = explode(' ', $promote_end);
            $date = explode('-', $dateTime[0]);
            $time = explode(':', $dateTime[1]);

            $promote_end = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
        } else {
            show_system_message('时间格式不正确', array());
            exit;
        }
    } else {
        $promote_end = 0;
    }

    $weight = ( 0 >= $weight ) ? 0 : $weight;

    $order_view = ( 0 >= $order_view ) ? 50 : $order_view;

    $free_delivering = ( 1 == $free_delivering ) ? 1 : 0;

    $check_product_sn = 'select `product_sn` from '.$db->table('product').' where product_sn = \''.$product_sn.'\' limit 1';
    $product = $db->fetchRow($check_product_sn);
    if( $product ) {
        show_system_message('产品编号已被使用', array());
        exit;
    }

    $check_category = 'select * from '.$db->table('category').' where id = '.$category.' and business_account = \''.$_SESSION['business_account'].'\' limit 1';
    $category_exists = $db->fetchRow($check_category);
    if( !$category_exists ) {
        show_system_message('不存在的产品分类', array());
        exit;
    }

    $check_type = 'select * from '.$db->table('product_type').' where id = '.$product_type.' limit 1';
    $type_exists = $db->fetchRow($check_type);
    if( !$type_exists ) {
        show_system_message('不存在的产品类型', array());
        exit;
    }
    $check_brand = 'select * from '.$db->table('brand').' where id = '.$brand.' limit 1';
    $brand_exists = $db->fetchRow($check_brand);
    if( !$brand_exists ) {
        show_system_message('不存在的品牌', array());
        exit;
    }

    $data = array(
        'product_sn' => $product_sn,
        'name' => $name,
        'shop_price' => $shop_price,
        'price' => $shop_price,
        'lowest_price' => $lowest_price,
        'reward' => $reward,
        'integral' => $integral,
        'integral_given' => $given_integral,
        'img' => $img,
        'desc' => $desc,
        'detail' => $detail,
        'business_account' => $_SESSION['business_account'],
        'category_id' => $category,
        'product_type_id' => $product_type,
        'status' => $status,
        'promote_price' => $promote_price,
        'promote_begin' => $promote_begin,
        'promote_end' => $promote_end,
        'add_time' => time(),
        'weight' => $weight,
        'brand_id' => $brand,
        'order_view' => $order_view,
        'free_delivery' => $free_delivering,
    );
    $table = 'product';

    if( $db->autoInsert($table, array($data)) ) {
        $links = array(
            array('link' => 'product.php', 'alt' => '产品列表'),
            array('link' => 'product.php?act=add', 'alt' => '继续添加'),
        );


        if( is_array($attr) ) {
            foreach( $attr as $k => $v ) {
                $attributes = json_encode($v);
                $data = array(
                    'product_sn' => $product_sn,
                    'attributes' => $attributes,
                    'inventory' => $inventory,
                );
                $table = 'inventory';
                if( !$db->autoInsert($table, array($data)) ) {
                    show_system_message('添加产品属性失败', $links);
                }
            }
        }
        show_system_message('增加产品成功', $links);
        exit;

    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }

}

if( 'edit' == $opera ) {

}

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

