<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/23
 * Time: 下午3:07
 */
include 'library/init.inc.php';

$operation = 'get_fav';
$opera = check_action($operation, getPOST('opera'));

$response = array('error'=>1, 'msg'=>'');

if('get_fav' == $opera)
{
    $get_fav_products = 'select `name`,`price`,`img`,`id` from '.$db->table('product').' order by `add_time` DESC limit 3';
    $fav_products = $db->fetchAll($get_fav_products);
    assign('product_list', $fav_products);

    $response['error'] = 0;
    $response['content'] = $smarty->fetch('product_item.phtml');
}

echo json_encode($response);
exit;