<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/10/20
 * Time: 下午12:04
 */
include 'library/init.inc.php';

$mch_key = 'CeciliaZhengWinsenPengwrhltx2015';
//仅对微信支付的异步通知
//$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
$xml=<<<XML
<xml><appid><![CDATA[wx81e8ce31ddb9082b]]></appid>
<bank_type><![CDATA[ICBC_DEBIT]]></bank_type>
<cash_fee><![CDATA[1990]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[Y]]></is_subscribe>
<mch_id><![CDATA[1286316801]]></mch_id>
<nonce_str><![CDATA[mGkoN1xEpyJVh2H3z4d2sUUWBlnmXpGz]]></nonce_str>
<openid><![CDATA[oYCOEv7DOXxrzIrPVv1EMHkHMHqI]]></openid>
<out_trade_no><![CDATA[14521001043332]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[6E58569038391D9D12E91A93703307BE]]></sign>
<time_end><![CDATA[20160107010834]]></time_end>
<total_fee>1990</total_fee>
<trade_type><![CDATA[JSAPI]]></trade_type>
<transaction_id><![CDATA[1001500710201601072550163055]]></transaction_id>
</xml>
XML;
$log->record($xml);
$data = simplexml_load_string($xml);
$success_response =<<<XML
<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>
XML;
$log->record('request notify.php');
if($data)
{
    if($data->return_code == 'SUCCESS')
    {
        //支付成功
        $sn = $data->out_trade_no;
        $sn = $db->escape($sn);

        $pattern = '/R.*/';
        $copartner_pattern = '/SJ.*/';
        $data->sign = strtolower($data->sign);

        if(preg_match($pattern, $sn)) {
            //充值订单
            $get_recharge_info = 'select `account`,`amount` from ' . $db->table('recharge') . ' where `recharge_sn`=\'' . $sn . '\'';
            $recharge = $db->fetchRow($get_recharge_info);

            if ($recharge && $recharge['amount'] * 100 == $data->total_fee && $data->sign == tenpay_sign($data, $mch_key)) {
                $log->record($sn . '支付成功');
                //验证充值金额正确
                $recharge_data = array(
                    'status' => 1
                );
                $flag = $db->autoUpdate('recharge', $recharge_data, '`recharge_sn`=\'' . $sn . '\'');
                if ($flag && $db->get_affect_rows()) {
                    add_memeber_exchange_log($recharge['account'], $recharge['amount'], 0, 0, 0, 0, $recharge['account'], $recharge['account'] . '在线充值');

                    add_recharge_log($sn, $recharge['account'], $recharge['account'], 0, 1, '在线充值');
                    $log->record('充值成功,成功更新充值记录');
                }
            } else {
                //充值金额不正确或返回不正确
            }

        } elseif(preg_match($copartner_pattern, $sn)) {
            $account = explode('-', $sn);
            $account = $account[0];
            $account = $db->escape($account);
            //支付成功
            if( $config['partner_fee'] * 100 == $data->total_fee  && $data->sign == tenpay_sign($data, $mch_key) ) {
                //是否已是合伙人
                $get_level_id = 'select `level_id` from '.$db->table('member').' where account = \''.$account.'\' limit 1';
                $level_id = $db->fetchOne($get_level_id);

                if( $level_id == 0 ) {
                    $update_member = 'update '.$db->table('member').' set level_id = 1';
                    $update_member .= ' where account = \''.$account.'\' limit 1';
                    $db->update($update_member);
                    //赠送积分给合伙人
                    add_memeber_exchange_log($account, 0, 0, $config['level_integral'], 0, 0, $account, '成为合伙人赠送积分');

                    $get_path = 'select `path` from '.$db->table('member').' where `account`=\''.$account.'\'';
                    $path = $db->fetchOne($get_path);

                    distribution_settle($config['partner_fee'] , $path, $account.'成为合伙人');
                }

            } else {

            }
        } else {
            //支付成功
            $sn = $data->out_trade_no;
            $sn = $db->escape($sn);

            //产品订单
            $get_order_info = 'select * from '.$db->table('order').' where `order_sn`=\''.$sn.'\'';

            $order = $db->fetchRow($get_order_info);

            add_order_log($sn, $order['account'], 3, "在线支付");
            $log->record_array($order);

            $log->record(($order['amount']*100).'=='.$data->total_fee.','.$data->sign.'=='.tenpay_sign($data, $mch_key));
            $amount_equal = $order['amount'] == $data->total_fee/100 ? 'true' : 'false';
            $log->record('amount_equal='.$amount_equal);

            $sign_equal = $data->sign == tenpay_sign($data, $mch_key) ? 'true' : 'false';
            $log->record('sign_equal='.$sign_equal);

            if($order && $order['amount'] == $data->total_fee/100 && $data->sign == tenpay_sign($data, $mch_key))
            {
                //验证订单金额正确
                //1. 设置订单为已付款
                $order_data = array(
                    'status' => 3,
                    'pay_time' => time()
                );

                $flag = $db->autoUpdate('order', $order_data, '`order_sn`=\''.$sn.'\' and `status`<3');
                if($flag && $db->get_affect_rows())
                {
                    $log->record($sn.'支付成功');
                    //2. 订单结算
                    $get_path = 'select `path` from '.$db->table('member').' where `account`=\''.$order['account'].'\'';
                    $path = $db->fetchOne($get_path);
                    distribution_settle($order['reward_amount'], $path, $sn);
                    //3. 新增商家收入
                    $business_income = $order['product_amount']+$order['delivery_fee'] - $order['reward_amount'];
                    if(add_business_exchange($order['business_account'], 0, $business_income, $order['account'], '用户在线支付'))
                    {
                        add_business_trade($order['business_account'], $business_income, $sn);
                    } else {
                        //增加商家收入失败
                    }

                    $get_order_detail = 'select `product_sn`,`product_name`,`count`,`is_virtual`,`attributes` from '.$db->table('order_detail').' where `order_sn`=\''.$sn.'\'';
                    $order_detail = $db->fetchAll($get_order_detail);
                    foreach($order_detail as $od)
                    {
                        //状态变为已发货
                        $delivery = false;
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
                        } else{
                            $delivery = true;
                        }
                    }
                    if( $delivery ) {
                        $order_data = array(
                            'status' => 4,
                        );
                        $db->autoUpdate('order', $order_data, '`order_sn`=\''.$sn.'\' and `status`<>4');
                    }

                    //如果会员购买了activity=4的产品，则升级
                    $check_can_levelup = 'select am.`activity_id` from '.$db->table('activity_mapper').' as am left join '.
                        $db->table('order_detail').' using (`product_sn`) where `order_sn`=\''.$sn.'\' and `activity_id`=4';
                    if($db->fetchOne($check_can_levelup))
                    {
                        $member_data = array(
                            'level_id' => 1
                        );

                        $db->autoUpdate('member', $member_data, '`account`=\''.$order['account'].'\'');
                    }
                }
            } else {
                //金额不正确
                $log->record($data->out_trade_no.'支付金额不正确');
                $log->record_array($data);
            }
        }
    } else {
        //支付失败
        $log->record($data->out_trade_no.'支付失败');
        $log->record_array($data);
    }
} else {
    //没有接收结果
    $log->record('没有接收到信息');
}

echo $success_response;