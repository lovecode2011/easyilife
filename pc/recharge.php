<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-23
 * @version 
 */

include 'library/init.inc.php';

$operation = 'wechat|alipay';
$opera = check_action($operation, getPOST('opera'));

//支付宝
if( 'alipay' == $opera ){
    $response = array('msg' => '', 'error' => 1);

    $amount = getPOST('amount');
    $amount = floatval($amount);
    if($amount <= 0) {
        $response['msg'] = '充值金额必须大于0';
    } else {
        $total_fee = $amount;
        $recharge_sn = add_recharge($_SESSION['account'], $total_fee);
        if ($recharge_sn) {
            $_SESSION['order_sn'] = $recharge_sn;

            require_once("alipay/alipay.config.php");
            require_once("alipay/lib/alipay_submit.class.php");

            /**************************请求参数**************************/

            //支付类型
            $payment_type = "1";
            //必填，不能修改
            //服务器异步通知页面路径
            $notify_url = "http://".$_SERVER['HTTP_HOST']."/alipay/notify_url.php";
            //需http://格式的完整路径，不能加?id=123这类自定义参数

            //页面跳转同步通知页面路径
            $return_url = "http://".$_SERVER['HTTP_HOST']."/payresult.php";
            //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

            //商户订单号
            $out_trade_no = $recharge_sn;
            //商户网站订单系统中唯一订单号，必填

            //订单名称
            $subject = $recharge_sn;
            //必填

            //付款金额
            $total_fee = $amount;
            //必填

            //订单描述

            $body = $config['site_name'].'充值';
            //商品展示地址
            $show_url = "http://".$_SERVER['HTTP_HOST'];
            //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

            //防钓鱼时间戳
            $anti_phishing_key = "";
            //若要使用请调用类文件submit中的query_timestamp函数

            //客户端的IP地址
            $exter_invoke_ip = "";
            //非局域网的外网IP地址，如：221.0.0.1


            /************************************************************/

            //构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => "create_direct_pay_by_user",
                "partner" => trim($alipay_config['partner']),
                "seller_email" => trim($alipay_config['seller_email']),
                "payment_type"	=> $payment_type,
                "notify_url"	=> $notify_url,
                "return_url"	=> $return_url,
                "out_trade_no"	=> $out_trade_no,
                "subject"	=> $subject,
                "total_fee"	=> $total_fee,
                "body"	=> $body,
                "show_url"	=> $show_url,
                "anti_phishing_key"	=> $anti_phishing_key,
                "exter_invoke_ip"	=> $exter_invoke_ip,
                "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
            );

            $log->record_array($parameter);
            //建立请求
            $alipaySubmit = new AlipaySubmit($alipay_config);
            $response['msg'] = '';
            $response['content'] = $alipaySubmit->buildRequestForm($parameter,"get", "确认");

            $response['error'] = 0;
        } else {
            $response['msg'] = '订单错误';
        }
    }
    echo json_encode($response);
    exit;
}


//获取用户信息
$get_user_info = 'select * from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$user_info = $db->fetchRow($get_user_info);
assign('user_info', $user_info);

$smarty->display('recharge.phtml');