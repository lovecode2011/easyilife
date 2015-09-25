<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/22
 * Time: 上午7:17
 */
include 'library/init.inc.php';

//获取上一级用户信息
$get_parent_id = 'select `parent_id` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$parent_id = $db->fetchOne($get_parent_id);

$get_product_list = '';
$user = null;
if($parent_id == 0)
{
    $user = array(
        'nickname' => '生活圈',
        'headimg' => 'themes/easyilife/images/slj.jpg'
    );
    $get_product_list = 'select `name`,`price`,`id`,`img` from '.$db->table('product');
} else {
    $get_user_info = 'select `nickname`,`headimg`,`account` from '.$db->table('member').' where `id`='.$parent_id;
    $user = $db->fetchRow($get_user_info);

    $get_product_sns = 'select `product_sn` from '.$db->table('distribution').' where `account`=\''.$user['account'].'\'';
    $product_sns = $db->fetchAll($get_product_sns);

    $product_sn_str = implode('\', \'', $product_sns);
    $get_product_list = 'select `name`,`price`,`id`,`img` from '.$db->table('product').' where `product_sn` in (\''.$product_sn_str.'\')';
}

$business = array(
    'shop_name' => $user['nickname'],
    'shop_logo' => $user['headimg']
);

assign('business', $business);
//获取平台分类
$get_category = 'select `id`,`name` from '.$db->table('category').' where `business_account`=\'\'';
$category = $db->fetchAll($get_category);
foreach($category as $key=>$c)
{
    $get_children = 'select `id`,`name` from '.$db->table('category').' where `parent_id`='.$c['id'];

    $category[$key]['children'] = $db->fetchAll($get_children);
}

assign('category', $category);
//获取商家全部产品
$product_list = $db->fetchAll($get_product_list);
assign('product_list', $product_list);
//获取新添加的产品
$get_product_list = 'select `name`,`price`,`id`,`img` from '.$db->table('product').' where `add_time`>'.(time()-3600*24*7);
$new_product = $db->fetchAll($get_product_list);
assign('new_product_count', count($new_product));

//获取猜你喜欢
$get_fav_products = 'select `name`,`price`,`img`,`id` from '.$db->table('product').' order by `add_time` DESC limit 3';
$fav_products = $db->fetchAll($get_fav_products);
assign('fav_products', $fav_products);

$smarty->display('distribution_shop.phtml');