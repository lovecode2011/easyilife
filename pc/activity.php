<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 下午8:21
 */
include 'library/init.inc.php';
$template = 'product-list.phtml';

$action = 'add_to_cart';
$act = check_action($action, getGET('act'));

$id = intval(getGET('id'));

$product_list = array();

$flag = false;

$operation = 'sort';
$opera = check_action($operation, getPOST('opera'));

//产品排序
if('sort' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $filter = getPOST('filter');
    $mode = getPOST('mode');

    $now = time();
    $get_product_list = 'select `id`,`name`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img` from '.$db->table('product').' where  `status`=4 ';

    $response['filter'] = $filter;

    //分组使用筛选条件
    //关键词
    if(isset($filter['id']) && $filter['id'] > 0)
    {
        $id = intval($filter['id']);
        $get_product_sn = 'select `product_sn` from '.$db->table('activity_mapper').' where `activity_id`='.$id;
        $product_sn_arr = $db->fetchAll($get_product_sn);
        $product_sn_str = '';
        if($product_sn_arr)
        {
            foreach ($product_sn_arr as $p) {
                $product_sn_str .= '\'' . $p['product_sn'] . '\',';
            }
        }

        $product_sn_str = substr($product_sn_str, 0, strlen($product_sn_str)-1);
        $get_product_list .= ' and `category_id` in ('.$product_sn_str.') and `status`=4';
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

    $response['sql'] = $get_product_list;
    $product_list = $db->fetchAll($get_product_list);

    assign('product_list', $product_list);
    $response['content'] = $smarty->fetch('product-list-item.phtml');
    $response['error'] = 0;

    echo json_encode($response);
    exit;
}

if($id < 0)
{
    redirect('index.php');
}

$state = getGET('state');
$state_list = 'sale_amount|price|discount|star|add_time';

$state = check_action($state_list, $state);

if('' == $state)
{
    $state = 'price';
}

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
        $order .= ' order by comment_count desc';
        $filter = 'comment';
        break;
    default:
        $order .= '';
        break;
}
assign('filter', $filter);

$get_activity_name = 'select `name` from '.$db->table('activity').' where `id`='.$id;
$activity_name = $db->fetchOne($get_activity_name);
assign('category_name', $activity_name);

$get_product_sn = 'select `product_sn` from '.$db->table('activity_mapper').' where `activity_id`='.$id;
$product_sn_arr = $db->fetchAll($get_product_sn);
$product_sn_str = '';

if($product_sn_arr)
{
    foreach ($product_sn_arr as $p) {
        $product_sn_str .= '\'' . $p['product_sn'] . '\',';
    }
}

$product_sn_str = substr($product_sn_str, 0, strlen($product_sn_str)-1);

$count = 12;
$offset = 0;
assign('count', $count);

$get_total = 'select count(*) from '.$db->table('product').' where `status`=4 and `product_sn` in ('.$product_sn_str.')';
$total = $db->fetchOne($get_total);
$total_page = ceil( $total / $count );

$page = intval(getGET('page'));
$page = ( $page > $total_page ) ? $total_page : $page;
$page = ( 0 >= $page ) ? 1 : $page;
$offset = ($page - 1) * $count;
create_pager($page, $total_page, $total);

$sub_select_comment = ' 0 as comment_count ';
$sub_select_collection = ' 0 as collection';

if(isset($_SESSION['account']))
{
//子查询是否已收藏
    $sub_select_collection = '(select `add_time` from ' . $db->table('collection') . ' where `product_sn` = p.`product_sn` and `account` = \'' . $_SESSION['account'] . '\') as collection';

//子查询评论数量
    $sub_select_comment = '(select count(comment) from ' . $db->table('comment') . ' where `parent_id` = 0 and `product_sn` = p.`product_sn`) as comment_count';
}

$now = time();
$get_product_list = 'select p.`product_sn`,p.`name`,p.`id`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img`,'.$sub_select_collection.','.$sub_select_comment.' from '.$db->table('product').'as p where `status`=4 and `product_sn` in ('.$product_sn_str.')';
$get_product_list .= $order;
$get_product_list .= ' limit '.$offset.','.$count;
$product_list = $db->fetchAll($get_product_list);
//assign('state', $state);
assign('product_list', $product_list);
assign('id', $id);

//assign('filter', json_encode($filter));
$smarty->display($template);