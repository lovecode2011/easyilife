<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/10/20
 * Time: 下午12:04
 */
include 'library/init.inc.php';

$mch_key = 'WinsenCeciliaWrhLtx2015KWANSONCC';
//仅对微信支付的异步通知
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];;
$log->record($xml);
$data = simplexml_load_string($xml);
$success_response =<<<XML
<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>
XML;


if($data)
{
    if($data->return_code == 'SUCCESS')
    {
        //支付成功
        $sn = $data->out_trade_no;
        $sn = $db->escape($sn);

        $pattern = '/R.*/';
        $data->sign = strtolower($data->sign);

        if(preg_match($pattern, $sn))
        {
            //充值订单
            $get_recharge_info = 'select `account`,`amount` from '.$db->table('recharge').' where `recharge_sn`=\''.$sn.'\'';
            $recharge = $db->fetchRow($get_recharge_info);

            if($recharge && $recharge['amount']*100 == $data->total_fee && $data->sign == tenpay_sign($data, $mch_key))
            {
                $log->record($sn.'支付成功');
                //验证充值金额正确
                $recharge_data = array(
                    'status' => 1
                );
                $flag = $db->autoUpdate('recharge', $recharge_data, '`recharge_sn`=\''.$sn.'\'');
                if($flag && $db->get_affect_rows())
                {
                    add_memeber_exchange_log($recharge['account'], $recharge['amount'], 0, 0, 0, 0, $recharge['account'], $recharge['account'].'在线充值');

                    add_recharge_log($sn, $recharge['account'], $recharge['account'], 0, 1, '在线充值');
                    $log->record('充值成功,成功更新充值记录');
                }
            } else {
                //充值金额不正确或返回不正确
            }
        } else {
            //支付成功
            $sn = $data->out_trade_no;
            $sn = $db->escape($sn);
            //产品订单
            $get_order_info = 'select `amount`,`account`,`business_account`,`product_amount`,`mobile`,`delivery_fee` from '.$db->table('order').' where `order_sn`=\''.$sn.'\'';

            $order = $db->fetchRow($get_order_info);

            if($order && $order['amount']*100 == $data->total_fee && $data->sign == tenpay_sign($data, $mch_key))
            {
                //验证订单金额正确
                //1. 设置订单为已付款
                $order_data = array(
                    'status' => 3,
                    'pay_time' => time()
                );

                $flag = $db->autoUpdate('order', $order_data, '`order_sn`=\''.$sn.'\' and `status`<>3');
                if($flag && $db->get_affect_rows())
                {
                    $log->record($sn.'支付成功');
                    //2. 订单结算
                    $get_path = 'select `path` from '.$db->table('member').' where `account`=\''.$order['account'].'\'';
                    $path = $db->fetchOne($get_path);
                    distribution_settle($order['product_amount'], $path, $sn);
                    //3. 新增商家收入
                    if(add_business_exchange($order['business_account'], 0, $order['product_amount']+$order['delivery_fee'], $order['account'], '用户在线支付'))
                    {
                        add_business_trade($order['business_account'], $order['product_amount']+$order['delivery_fee'], $sn);
                    } else {
                        //增加商家收入失败
                    }

                    $get_order_detail = 'select `product_sn`,`product_name`,`count`,`is_virtual`,`attributes` from '.$db->table('order_detail').' where `order_sn`=\''.$sn.'\'';
                    $order_detail = $db->fetchAll($get_order_detail);
                    foreach($order_detail as $od)
                    {
                        //扣减库存
                        consume_inventory($od['product_sn'], $od['attributes'], $od['count']);
                        //如果是虚拟产品，则生成预约券
                        if($od['is_virtual'])
                        {
                            $get_virtual_contents = 'select `content`,`count`,`total` from ' . $db->table('virtual_content') . ' where `product_sn`=\'' . $od['product_sn'] . '\'';

                            $virtual_contents = $db->fetchAll($get_virtual_contents);

                            $virtual_content = '';
                            if ($virtual_contents)
                            {
                                $virtual_content = serialize($virtual_contents);
                            }

                            add_order_content($order['business_account'], $order['account'], $order['mobile'], $sn, $od['product_sn'], $od['product_name'], $virtual_content, 2);
                        }
                    }
                }
            } else {
                //金额不正确
            }
        }
    } else {
        //支付失败
    }
} else {
    //没有接收结果
}

echo $success_response;