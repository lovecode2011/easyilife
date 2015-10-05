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
    if(isset($filter['keyword']) && $filter['keyword'] != '')
    {
        $keyword = $db->escape($filter['keyword']);
        $get_product_list .= ' and `name` like \'%'.$keyword.'%\'';
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
assign('attributes', null);

assign('filter', json_encode($filter));
$smarty->display('search.phtml');