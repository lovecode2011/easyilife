<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-18
 * @version 
 */

include 'library/init.inc.php';
$operation = 'paging';
$opera = check_action($operation, getPOST('opera'));
$now = time();

$page_count = 9;
assign('page_count', $page_count);

//子查询评论数量
$sub_select_comment = '(select count(comment) from '.$db->table('comment').' where `parent_id` = 0 and `product_sn` = p.`product_sn`) as comment_count';

$get_product_list = 'select p.`product_sn`,p.`name`,p.`img`,p.`id`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,'.$sub_select_comment.' from '.$db->table('collection').' as d join '.$db->table('product').' as p '.
    ' using(`product_sn`) where d.`account`=\''.$_SESSION['account'].'\' order by d.`add_time` DESC';

$product_list = $db->fetchAll($get_product_list);

$total = count($product_list);
$total_page = ceil($total / $page_count);
assign('total_page', $total_page);


if( 'paging' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    if(!check_cross_domain() && isset($_SESSION['account']) ) {
        $page = intval(getPOST('page'));

        $page = $page > $total_page ? $total_page : $page;
        $page = $page < 1 ? 1 : $page;
        $offset = $page_count * ( $page - 1 );

        $get_product_list .= ' limit '.$offset.','.$page_count;
        $product_list = $db->fetchAll($get_product_list);

        assign('product_list', $product_list);
        assign('page', $page);
        $response['content'] = $smarty->fetch('collection-item.phtml');
        $response['error'] = 0;
    } else {
        if (empty($_SESSION['account'])) {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }
    echo json_encode($response);
    exit;
}

//猜你喜欢
$get_fav_products = 'select `name`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img`,`id` from '.$db->table('product').' where `status`=4 order by `add_time` DESC limit 8';
$fav_products = $db->fetchAll($get_fav_products);
assign('fav_products', $fav_products);

assign('product_list', $product_list);
assign('title', '我的收藏');
assign('mode', 'collection');
$smarty->display('wishlist.phtml');