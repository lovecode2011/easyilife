<?php
/**
 * 首页
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:11
 */
include 'library/init.inc.php';

//获取顶部导航栏
$get_top_nav = 'select * from '.$db->table('nav').' where `position`=\'top\' order by `order_view`';
$top_nav = $db->fetchAll($get_top_nav);
assign('top_nav', $top_nav);

//获取公告
$get_notice = 'select `id`,`title`,`add_time` from '.$db->table('content').' where `section_id`=1 and `status`=1 order by `add_time` DESC limit 5';
$notice = $db->fetchAll($get_notice);
assign('notice', $notice);

//获取轮播广告
$get_cycle_ad = 'select `img`,`url` from '.$db->table('ad').' where `ad_pos_id`=10 order by `order_view`';
$cycle_ad = $db->fetchAll($get_cycle_ad);
assign('cycle_ad', $cycle_ad);

//获取展示广告
$get_perform_ad = 'select `img`,`url`,`alt` from '.$db->table('ad').' where `ad_pos_id`=21 order by `order_view`';
$perform_ad = $db->fetchAll($get_perform_ad);
assign('perform_ad', $perform_ad);

//平台优选
$get_product_sn = 'select `product_sn` from '.$db->table('activity_mapper').' where `activity_id`=1';
$product_sn_arr = $db->fetchAll($get_product_sn);
$product_sn_str = '';

foreach($product_sn_arr as $p)
{
    $product_sn_str .= '\''.$p['product_sn'].'\',';
}

$product_sn_str = substr($product_sn_str, 0, strlen($product_sn_str)-1);

$now = time();
$get_product_list = 'select `product_sn`,`name`,`id`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img` from '.$db->table('product').' where `status`=4 and `product_sn` in ('.$product_sn_str.')';
$youxuan = $db->fetchAll($get_product_list);
assign('youxuan', $youxuan);

//
$get_product_sn = 'select `product_sn` from '.$db->table('activity_mapper').' where `activity_id`=2';
$product_sn_arr = $db->fetchAll($get_product_sn);
$product_sn_str = '';

foreach($product_sn_arr as $p)
{
    $product_sn_str .= '\''.$p['product_sn'].'\',';
}

$product_sn_str = substr($product_sn_str, 0, strlen($product_sn_str)-1);

$now = time();
$get_product_list = 'select `product_sn`,`name`,`id`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img` from '.$db->table('product').' where `status`=4 and `product_sn` in ('.$product_sn_str.')';
$cuxiao = $db->fetchAll($get_product_list);
assign('cuxiao', $cuxiao);

//
$get_product_sn = 'select `product_sn` from '.$db->table('activity_mapper').' where `activity_id`=3';
$product_sn_arr = $db->fetchAll($get_product_sn);
$product_sn_str = '';

if($product_sn_arr) {
    foreach ($product_sn_arr as $p) {
        $product_sn_str .= '\'' . $p['product_sn'] . '\',';
    }
}

$product_sn_str = substr($product_sn_str, 0, strlen($product_sn_str)-1);

$now = time();
$get_product_list = 'select `product_sn`,`name`,`id`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img` from '.$db->table('product').' where `status`=4 and `product_sn` in ('.$product_sn_str.')';
$jifen = $db->fetchAll($get_product_list);
assign('jifen', $jifen);

$smarty->display('index.phtml');
