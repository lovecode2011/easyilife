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
$template = 'search-result.phtml';

$count = 12;
$offset = 0;
assign('count', $count);

if('sort' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $filter = getPOST('filter');

    $now = time();
    $get_product_list = 'select p.`id`,p.`name`,if(p.`promote_end`>'.$now.',p.`promote_price`,p.`price`) as `price`,p.`img`,p.`product_sn`,(select `account` from '.$db->table('collection').
        ' where `account`=\''.$_SESSION['account'].'\' and `product_sn`=p.`product_sn`) as collection from ' . $db->table('product') .
        ' as p where p.`status`=4 ';


    $response['filter'] = $filter;

    //分组使用筛选条件
    //关键词
    if(isset($filter['keyword']) && $filter['keyword'] != '')
    {
        $keyword = $db->escape($filter['keyword']);
        $get_product_list .= ' and p.`name` like \'%'.$keyword.'%\'';
    }

    $promote_condition = '';
    //价格区间
    if(isset($filter['price_l']))
    {
        $price_l = $filter['price_l'];
        $price_l = floatval($price_l);
        $get_product_list .= ' and `price`>='.$price_l;
        $promote_condition .= ' and `promote_price`>='.$price_l;
    }

    if(isset($filter['price_h']))
    {
        $price_h = $filter['price_h'];
        $price_h = floatval($price_h);
        $get_product_list .= ' and `price`<='.$price_h;
        $promote_condition .= ' and `promote_price`<='.$price_h;
    }

    if($promote_condition != '')
    {
        $get_product_list .= ' or (`promote_end`>'.$now.$promote_condition.')';
    }

    //免运费
    if(isset($filter['free_delivery']))
    {
        $get_product_list .= ' and p.`free_delivery`=1';
    }
    //积分换购
    if(isset($filter['integral_exchange']))
    {
        $get_product_list .= ' and p.`integral`>0';
    }

    switch($mode)
    {
        case 'sale':
            $get_product_list .= ' order by p.`sale_count` DESC';
            break;
        case 'star':
            $get_product_list .= ' order by p.`star` DESC';
            break;
        case 'price':
            $orderby = getPOST('orderby');
            $orderby_list = 'up|down';
            $orderby = check_action($orderby_list, $orderby);
            if($orderby == '')
            {
                $orderby = 'up';
            }

            if($orderby == 'up')
            {
                $get_product_list .= ' order by `price` ASC';
            } else {
                $get_product_list .= ' order by `price` DESC';
            }
            break;
        case 'new':
            $get_product_list .= ' order by p.`add_time` DESC';
            break;
        default:
            break;
    }

    $product_list = $db->fetchAll($get_product_list);

    assign('product_list', $product_list);
    $response['content'] = $smarty->fetch('search-product-item.phtml');
    $response['error'] = 0;

    echo json_encode($response);
    exit;
}

$keyword = getGET('keyword');
$keyword = $db->escape($keyword);

$mode = getGET('mode');
$mode_list = 'shop|product';
$mode = check_action($mode_list, $mode);
if($mode == '')
{
    $mode = 'product';
}
$mode_name = array(
    'product' => '宝贝',
    'shop' => '店铺',
);
assign('mode', $mode);
assign('mode_name', $mode_name[$mode]);
assign('keyword', $keyword);
if($mode == 'product') {

    $get_total = 'select count(*) from ' . $db->table('product') . ' where `status`=4 and `name` like \'%' . $keyword . '%\'';
    $total = $db->fetchOne($get_total);
    $total_page = ceil($total / $count);

    $page = intval(getGET('page'));
    $page = ($page > $total_page) ? $total_page : $page;
    $page = (0 >= $page) ? 1 : $page;
    $offset = ($page - 1) * $count;
    create_pager($page, $total_page, $total);

    $filter = trim(getGET('filter'));
    $filter = $db->escape($filter);
    $order = '';
    switch ($filter) {
        case 'sale':
            $order .= ' order by sale_count desc';
            $filter = 'sale';
            break;
        case 'price1':
            $order .= ' order by price desc';
            $filter = 'price1';
            break;
        case 'price2':
            $order .= ' order by price asc';
            $filter = 'price2';
            break;
        case 'comment':
            $order .= ' order by comment desc';
            $filter = 'comment';
            break;
        default:
            $order .= '';
            break;
    }
    assign('filter', $filter);

    $now = time();
    $get_product_list = 'select p.`id`,p.`name`,if(p.`promote_end`>' . $now . ',p.`promote_price`,p.`price`) as `price`,p.`img`,p.`product_sn`,(select `account` from ' . $db->table('collection') .
        ' where `account`=\'' . $_SESSION['account'] . '\' and `product_sn`=p.`product_sn`) as collection ' .
        ' ,(select count(id) from ' . $db->table('comment') . ' where parent_id = 0 and product_sn = p.product_sn) as comment' .
        ' ,(select sum(count) from ' . $db->table('order_detail') . ' where product_sn = p.product_sn) as sale_count' .
        ' from ' . $db->table('product') .
        ' as p where p.`status`=4 and p.`name` like \'%' . $keyword . '%\'';
    $get_product_list .= $order;
    $get_product_list .= ' limit ' . $offset . ',' . $count;
//echo $get_product_list;exit;

    $product_list = $db->fetchAll($get_product_list);

    assign('product_list', $product_list);

    $smarty->display($template);
} else {

    $get_total = 'select count(*) from ' . $db->table('business') . ' where `status`=2 and `shop_name` like \'%' . $keyword . '%\'';
    $total = $db->fetchOne($get_total);
    $total_page = ceil($total / $count);

    $page = intval(getGET('page'));
    $page = ($page > $total_page) ? $total_page : $page;
    $page = (0 >= $page) ? 1 : $page;
    $offset = ($page - 1) * $count;
    create_pager($page, $total_page, $total);


    $filter = trim(getGET('filter'));
    $filter = $db->escape($filter);
    $order = '';
    switch ($filter) {
        default:
            $order .= 'order by comment desc';
            break;
    }
    assign('filter', $filter);

    $get_shop_list = 'select `id`, `shop_name`,`comment`,`shop_logo`,`business_account` from '.$db->table('business').' where '.
        ' `status`=2 and `shop_name` like \'%'.$keyword.'%\'';

    $shop_list = $db->fetchAll($get_shop_list);
    if($shop_list)
    {
        foreach ($shop_list as $key => $s)
        {
            $get_product_list = 'select `id`,`img`,`name`,`price` from ' . $db->table('product') .
                ' where `business_account`=\'' . $s['business_account'] . '\' and `status`=4 order by `star` DESC limit 3';
            $shop_list[$key]['product_list'] = $db->fetchAll($get_product_list);
        }
    }
    assign('shop_list', $shop_list);

//    assign('filter', json_encode($filter));
    assign('filter', $filer);
    $template = 'search-shop.phtml';
    $smarty->display($template);
}
