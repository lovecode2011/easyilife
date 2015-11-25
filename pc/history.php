<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-19
 * @version 
 */

include 'library/init.inc.php';

$operation = 'delete';
$opera = check_action($operation, getPOST('opera'));

if( 'delete' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    $pattern = '/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)\ \d{1,2}:\d{1,2}:\d{1,2}$/';
    if(!check_cross_domain() && isset($_SESSION['account']) ) {
        $date = trim(getPOST('date'));
        if( '' == $date ) {
            $response['msg'] = '参数错误1';
        } elseif( preg_match($pattern, $date) ) {
            $response['msg'] = '参数错误2';
        } else {
            $target_date = date('Y-m-d', strtotime($date));
            $delete_history = 'delete from '.$db->table('history').' where `account`=\''.$_SESSION['account'].'\' and add_time like \''.$target_date.'%\'';
            if( $db->delete($delete_history) ) {
                $response['error'] = 0;
                $response['date'] = $target_date;
            } else {
                $response['msg'] = '系统繁忙，请稍后重试';
//                $response['msg'] = $delete_history;
            }
        }
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

$now = time();
$get_product_list = 'select p.`product_sn`,p.`name`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,p.`id`,p.`img`,d.`add_time` from '.$db->table('history').' as d join '.$db->table('product').' as p '.
    ' using(`product_sn`) where d.`account`=\''.$_SESSION['account'].'\' order by d.`add_time` DESC';

$product_list = $db->fetchAll($get_product_list);

$now = time();
//猜你喜欢
$get_fav_products = 'select `name`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img`,`id` from '.$db->table('product').' where `status`=4 order by `add_time` DESC limit 8';
$fav_products = $db->fetchAll($get_fav_products);
assign('fav_products', $fav_products);
$target_list = array();

if($product_list) {
    foreach ($product_list as $product) {
        $add_date_time = strtotime($product['add_time']);
        $add_date = date('Y-m-d', $add_date_time);
        $target_list[$add_date][] = $product;
    }
}


assign('product_list', $target_list);

assign('title', '我的足迹');
assign('mode', 'history');
$smarty->display('history.phtml');