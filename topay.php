<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 上午8:49
 */
include 'library/init.inc.php';

$operation = 'wechat|alipay';
$opera = check_action($operation, getPOST('opera'));

//支付方式变更时生成支付代码
if('wechat' == $opera)
{
    $mch_id = '1269390401';
    $mch_key = 'CeciliaZhengWinsenPengwrhltx2015';

    $response = array('error'=>1, 'msg'=>'');

    $_SESSION['payment'] = 'wechat';

    $order_sn = $_SESSION['order_sn'];

    $get_order_info = 'select * from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\' and `account`=\''.$_SESSION['account'].'\'';

    $order = $db->fetchRow($get_order_info);

    $total_fee = $order['amount'];
    $detail = '订单编号:'.$order_sn;

    $response['price'] = '￥'.sprintf('%.2f', $total_fee);

    $body = $config['site_name'].'订单收款';
    $body = $detail;
    $out_trade_no = $order_sn;

    $res = create_prepay($config['appid'], $mch_id, $mch_key, $_SESSION['openid'], $total_fee, $body, $detail, $out_trade_no);

    $res = simplexml_load_string($res);

    if($res->prepay_id)
    {
        $response['error'] = 0;
    } else {
        $response['msg'] = $res->return_code.','.$res->return_msg;
    }

    $nonce_str = get_nonce_str();
    $response['nonce_str'] = $nonce_str;
    $time_stamp = time();

    //最后参与签名的参数有appId, timeStamp, nonceStr, package, signType。
    $sign = 'appId='.$config['appid'].'&nonceStr='.$nonce_str.'&package=prepay_id='.$res->prepay_id.'&signType=MD5&timeStamp='.$time_stamp.'&key='.$mch_key;
    $sign = md5($sign);
    $sign = strtoupper($sign);
    $response['timestamp'] = $time_stamp;
    $response['sign'] = $sign;
    $response['prepay_id'] = "".$res->prepay_id;

    echo json_encode($response);
    exit;
}

$order_sn = $_SESSION['order_sn'];

$get_order_info = 'select * from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\' and `account`=\''.$_SESSION['account'].'\'';

$order = $db->fetchRow($get_order_info);
assign('order', $order);

$get_order_detail = 'select * from '.$db->table('order_detail').' where `order_sn`=\''.$order_sn.'\'';
$order_detail = $db->fetchAll($get_order_detail);
assign('order_detail', $order_detail);

//获取平台支付方式
$get_payment_list = 'select `id`,`name`,`plugins` from '.$db->table('payment').' where `status`=1';
$payment_list = $db->fetchAll($get_payment_list);
assign('payment_list', $payment_list);
$smarty->display('topay.phtml');