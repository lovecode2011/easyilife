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
    $mch_id = '1286316801';
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

if('alipay' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $_SESSION['payment'] = 'wechat';

    $order_sn = $_SESSION['order_sn'];

    $get_order_info = 'select * from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\' and `account`=\''.$_SESSION['account'].'\'';

    $order = $db->fetchRow($get_order_info);

    require_once("alipay/alipay.config.php");
    require_once("alipay/lib/alipay_submit.class.php");

    /**************************请求参数**************************/

    //支付类型
    $payment_type = "1";
    //必填，不能修改
    //服务器异步通知页面路径
    $notify_url = "http://".$_SERVER['HTTP_HOST']."/notify_url.php";
    //需http://格式的完整路径，不能加?id=123这类自定义参数

    //页面跳转同步通知页面路径
    $return_url = "http://".$_SERVER['HTTP_HOST']."/payresult.php";
    //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

    //商户订单号
    $out_trade_no = $order['order_sn'];
    //商户网站订单系统中唯一订单号，必填

    //订单名称
    $subject = '订单编号:'.$order_sn;
    //必填

    //付款金额
    $total_fee = $order['amount'];
    //必填

    //商品展示地址
    $show_url = 'http://'.$_SERVER['HTTP_HOST'];
    //必填，需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

    //订单描述
    $body = $subject;
    //选填

    //超时时间
    $it_b_pay = '';
    //选填

    //钱包token
    $extern_token = '';
    //选填


    /************************************************************/

    //构造要请求的参数数组，无需改动
    $parameter = array(
        "service" => "alipay.wap.create.direct.pay.by.user",
        "partner" => trim($alipay_config['partner']),
        "seller_id" => trim($alipay_config['seller_id']),
        "payment_type"	=> $payment_type,
        "notify_url"	=> $notify_url,
        "return_url"	=> $return_url,
        "out_trade_no"	=> $out_trade_no,
        "subject"	=> $subject,
        "total_fee"	=> $total_fee,
        "show_url"	=> $show_url,
        "body"	=> $body,
        "it_b_pay"	=> $it_b_pay,
        "extern_token"	=> $extern_token,
        "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
    );

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);
    $response['content'] = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
    $response['error'] = 0;

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
