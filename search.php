<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 下午3:34
 */
include 'library/init.inc.php';

$operation = 'sort';
$opera = getPOST('opera');

if('sort' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $filter = getPOST('filter');
    $mode = getPOST('mode');


    $get_product_list = 'select `id`,`name`,`price`,`img` from '.$db->table('product').' where 1 ';

    $response['filter'] = $filter;

    //分组使用筛选条件
    //关键词
    if(isset($filter['keyword']) && $filter['keyword'] != '')
    {
        $keyword = $db->escape($filter['keyword']);
        $get_product_list .= ' and `name` like \'%'.$keyword.'%\'';
    }

    //价格区间
    if(isset($filter['price_l']))
    {
        $price_l = $filter['price_l'];
        $price_l = floatval($price_l);
        $get_product_list .= ' and `price`>='.$price_l;
    }

    if(isset($filter['price_h']))
    {
        $price_h = $filter['price_h'];
        $price_h = floatval($price_h);
        $get_product_list .= ' and `price`<='.$price_h;
    }

    //免运费
    if(isset($filter['free_delivery']))
    {
        $get_product_list .= ' and `free_delivery`=1';
    }
    //积分换购
    if(isset($filter['integral_exchange']))
    {
        $get_product_list .= ' and `integral`>0';
    }

    switch($mode)
    {
        case 'sale':
            $get_product_list .= ' order by `sale_count` DESC';
            break;
        case 'star':
            $get_product_list .= ' order by `star` DESC';
            break;
        case 'price':
            $get_product_list .= ' order by `price` ASC';
            break;
        case 'new':
            $get_product_list .= ' order by `add_time` DESC';
            break;
        default:
            break;
    }

    $product_list = $db->fetchAll($get_product_list);

    assign('product_list', $product_list);
    $response['content'] = $smarty->fetch('search_product_item.phtml');
    $response['error'] = 0;

    echo json_encode($response);
    exit;
}

$keyword = getGET('keyword');

$keyword = $db->escape($keyword);

$get_product_list = 'select `id`,`name`,`price`,`img` from '.$db->table('product').' where `name` like \'%'.$keyword.'%\'';

$product_list = $db->fetchAll($get_product_list);

assign('product_list', $product_list);
assign('keyword', $keyword);

$filter = array();

$filter['keyword'] = $keyword;
//获取其他筛选条件
$where =  '`name` like \'%'.$keyword.'%\'';
//根据产品的分类获取筛选价格区间、品牌
$attributes = array();
$get_brand_ids = 'select DISTINCT `brand_id` from '.$db->table('product').' where '.$where;
$brand_ids = $db->fetchAll($get_brand_ids);
$brand_id_str = '';
if($brand_ids)
{
    foreach ($brand_ids as $bid)
    {
        $brand_id_str .= $bid['brand_id'].',';
    }
}
$brand_id_str = substr($brand_id_str, 0, strlen($brand_id_str)-1);
$get_brand_list = 'select `id`,`name` from '.$db->table('brand').' where `id` in ('.$brand_id_str.')';
$brand_list = $db->fetchAll($get_brand_list);
$attributes[] = array(
    'key'=>'brand',
    'name'=>'品牌',
    'values' => $brand_list
);
/*
$get_price_limit = 'select max(`price`) as `max`,min(`price`) as `min` from '.$db->table('product').' where '.$where;
$price_limit = $db->fetchRow($get_price_limit);

$divide = 1;
if($price_limit['max'] != $price_limit['min'])
{

}
*/

assign('attributes', $attributes);

assign('filter', json_encode($filter));
$smarty->display('search-result.phtml');
