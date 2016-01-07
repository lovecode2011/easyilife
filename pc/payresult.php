<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-12-28
 * @version 
 */
include 'library/init.inc.php';

if(isset($_SESSION['order_sn'])) {
    $pay_title = '支付成功，请等待后台确认';
    assign('pay_title', $pay_title);

    $order_sn = $_SESSION['order_sn'];

    $get_order_info = 'select * from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\'' . $_SESSION['account'] . '\'';

    $order = $db->fetchRow($get_order_info);
    assign('order', $order);

    $notify_time = date('Y-m-d H:i:s', $order['pay_time']);
    assign('notify_time', $notify_time);

    $get_order_detail = 'select a.order_sn, p.* from ' . $db->table('order_detail') . ' as a left join '.$db->table('product').' as p on a.product_sn = p.product_sn where a.`order_sn`=\'' . $order_sn . '\'';
    $order_detail = $db->fetchAll($get_order_detail);
    assign('order_detail', $order_detail);
} else {
    require_once("alipay/alipay.config.php");
    require_once("alipay/lib/alipay_notify.class.php");

    //计算得出通知验证结果
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyReturn();

    if($verify_result) {//验证成功
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //请在这里加上商户的业务逻辑程序代码
        //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

        //商户订单号

        $out_trade_no = $_GET['out_trade_no'];

        //支付宝交易号

        $trade_no = $_GET['trade_no'];

        //交易状态
        $trade_status = $_GET['trade_status'];
        $notify_time = $_GET['notify_time'];

        $order_sn = $out_trade_no;
        $order_sn = $db->escape($order_sn);

        if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
            $pattern = '/R.*/';
            $copartner_pattern = '/SJ.*/';

            if(preg_match($pattern, $sn)) {
                //合伙人
                $pay_title = '支付成功，您已经成为合伙人';
                $order['order_sn'] = $order_sn;
                $order['remark'] = '';
                $order_detail = array();

            } elseif( preg_match($copartner_pattern, $order_sn) ) {
                //充值
                $pay_title = '支付成功，请核实账户余额';
                $order['order_sn'] = $order_sn;
                $order['remark'] = '';
                $order_detail = array();

            } else {
                //订单支付
                $pay_title = '支付成功，请等待后台确认';
                $get_order_info = 'select * from ' . $db->table('order') . ' where `order_sn`=\'' . $order_sn . '\' and `account`=\'' . $_SESSION['account'] . '\'';

                $order = $db->fetchRow($get_order_info);

                $get_order_detail = 'select a.order_sn, p.* from ' . $db->table('order_detail') . ' as a left join '.$db->table('product').' as p on a.product_sn = p.product_sn where a.`order_sn`=\'' . $order_sn . '\'';
                $order_detail = $db->fetchAll($get_order_detail);
            }
            assign('order', $order);
            assign('order_detail', $order_detail);
            assign('pay_title', $pay_title);
            assign('notify_time', $notify_time);
        }
        else {
            echo "trade_status=".$_GET['trade_status'];
            exit;
        }

//        echo "验证成功<br />";

        //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
    else {
        //验证失败
        //如要调试，请看alipay_notify.php页面的verifyReturn函数
//        echo "验证失败";
    }
}
$smarty->display('payresult.phtml');