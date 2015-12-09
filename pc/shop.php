<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-20
 * @version 
 */

include 'library/init.inc.php';

$action = 'list|new';
$act = check_action($action, getGET('act'));

$act = $act == '' ? 'list' : $act;

$id = intval(getGET('id'));
if( $id <= 0 )
{
    redirect('index.php');
}

//获取商家信息
$get_business_info = 'select * from '.$db->table('business').' where `id`=\''.$id.'\'';
$business = $db->fetchRow($get_business_info);

if( empty($business) ) {
    redirect('index.php');
}

$sn = $business['business_account'];

assign('business', $business);
assign('id', $id);
//获取商家分类
$get_category = 'select `id`,`name` from '.$db->table('category').' where `business_account`=\''.$sn.'\' and `parent_id`='.$business['category_id'];
$category = $db->fetchAll($get_category);
foreach($category as $key=>$c)
{
    $get_children = 'select `id`,`name` from '.$db->table('category').' where `parent_id`='.$c['id'];

    $category[$key]['children'] = $db->fetchAll($get_children);
}

$query_string = $_SERVER['QUERY_STRING'];
if( !strpos($query_string, 'page=') ) {
    $query_string .= '&page=1';
}

$query_string = preg_replace('#page=[0-9]+#', 'page=%s', $query_string);

assign('query_string', $query_string);

assign('shop_category_list', $category);

$count = 10;
$offset = 0;
assign('count', $count);

if( 'new' == $act ) {

    $get_total = 'select count(*) from ' . $db->table('product') . ' where  `status`=4 and `business_account`=\'' . $sn . '\'' .
        ' and `add_time`>' . (time() - 3600 * 24 * 7);
    $total = $db->fetchOne($get_total);
    $total_page = ceil($total / $count);

    $page = intval(getGET('page'));
    $page = ($page > $total_page) ? $total_page : $page;
    $page = (0 >= $page) ? 1 : $page;
    $offset = ($page - 1) * $count;
    create_pager($page, $total_page, $total);

    //获取新添加的产品
    $get_product_list = 'select `name`,`price`,`id`,`img` from ' . $db->table('product') . ' where  `status`=4 and `business_account`=\'' . $sn . '\'' .
        ' and `add_time`>' . (time() - 3600 * 24 * 7);
    $get_product_list .= ' limit ' . $offset . ',' . $count;
    $new_product = $db->fetchAll($get_product_list);
    assign('product_list', $new_product);
}


if( 'list' == $act ) {
    $get_total = 'select count(*) from ' . $db->table('product') . ' where `status`=4 and `business_account`=\'' . $sn . '\'';
    $total = $db->fetchOne($get_total);
    $total_page = ceil($total / $count);

    $page = intval(getGET('page'));
    $page = ($page > $total_page) ? $total_page : $page;
    $page = (0 >= $page) ? 1 : $page;
    $offset = ($page - 1) * $count;
    create_pager($page, $total_page, $total);

//获取商家全部产品
    $now = time();
    $get_product_list = 'select `name`,if(`promote_end`>' . $now . ',`promote_price`,`price`) as `price`,`id`,`img` from ' . $db->table('product') . ' where `status`=4 and `business_account`=\'' . $sn . '\'';
    $get_product_list .= ' limit ' . $offset . ',' . $count;
    $product_list = $db->fetchAll($get_product_list);
    assign('product_list', $product_list);

}

$smarty->display('shop.phtml');
