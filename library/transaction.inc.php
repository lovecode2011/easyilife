<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/7
 * Time: 上午8:27
 */

/**
 * 检查产品库存
 * @param string $product_sn
 * @param string $attributes
 * @param int $number
 * @return bool
 */
function check_inventory($product_sn, $attributes, $number)
{
    global $db;
    $check_inventory = 'select `inventory_logic` from '.$db->table('inventory').' where `product_sn`=\''.$product_sn.'\' '.
                       ' and `attributes`=\''.$attributes.'\' and `inventory_logic`>='.$number;

    return $check_inventory;
}

/**
 * 扣减产品库存
 * @param string $product_sn
 * @param string $attributes
 * @param int $number
 * @param int $mode
 * @return bool
 */
function consume_inventory($product_sn, $attributes, $number, $mode = 0)
{
    global $db;
    global $log;

    $sql = 'update '.$db->table('inventory').' set ';
    switch($mode)
    {
        case 1:
            //扣减物理库存
            $sql .= ',`inventory`=`inventory`-'.$number.', `inventory_await`=`inventory_await`+'.$number;
            break;
        default:
            //扣减逻辑库存
            $sql .= ' `inventory_logic`=`inventory_logic`-'.$number.', `inventory_await`=`inventory_await`+'.$number;
            break;
    }

    $sql .= ' where `product_sn`=\''.$product_sn.'\' and `attributes`=\''.$attributes.'\'';

    $log->record($sql);
    return $db->update($sql);
}

/**
 * 修改产品库存
 * @param string $product_sn
 * @param string $attributes
 * @param int $number
 * @return bool
 */
function modify_inventory($product_sn, $attributes, $number)
{
    global $db;


}
/**
 * 三级分销结算
 * @param float $reward
 * @param string $path
 * @return bool
 */
function distribution_settle($reward, $path)
{
    global $db;
    global $config;

    $ids_arr = explode(',', $path);
    array_pop($ids_arr);

    $rebate = $reward;
    $current_id = array_pop($ids_arr);
    //检查是否第一次购买
    $get_account = 'select `account` from '.$db->table('member').' where `id`='.$current_id;
    $account = $db->fetchOne($get_account);

    $check_order = 'select count(*) from '.$db->table('order').' where `account`=\''.$account.'\'';
    if($db->fetchOne($check_order))
    {
        add_memeber_exchange_log($account, 0, 0, 0, 0, $rebate, 'settle', '系统结算');
        add_member_reward($account, $rebate, 0, '系统结算');
        $rebate = 0;
    }

    while(count($ids_arr) > 3)
    {
        array_shift($ids_arr);
    }

    $level = 1;
    while($id = array_pop($ids_arr))
    {
        $reward_tmp = $reward * $config['level_'.$level];

        $get_account = 'select `account` from '.$db->table('member').' where `id`='.$id;
        $account = $db->fetchOne($get_account);

        if($rebate)
        {
            add_memeber_exchange_log($account, 0, 0, 0, 0, $rebate, 'settle', '系统结算');
            add_member_reward($account, $rebate, 0, '系统结算');
            $rebate = 0;
        }

        if($reward_tmp)
        {
            add_memeber_exchange_log($account, 0, 0, 0, 0, $reward_tmp, 'settle', '系统结算');
            add_member_reward($account, $reward_tmp, 0, '系统结算');
        }
    }

    return true;
}

/**
 * 新增订单操作记录
 */
function add_order_log($order_sn, $operator, $status, $remark = '')
{
    global $db;

    $log_data = array(
        'order_sn' => $order_sn,
        'operator' => $operator,
        'status' => $status,
        'remark' => $remark
    );

    return $db->autoInsert('order_log', array($log_data));
}

 /**
 * 新增订单详情
 * @param string $product_sn
 * @param string $product_name
 * @param string $product_attributes
 * @param string $attributes
 * @param float $product_price
 * @param float $integral
 * @param float $integral_given
 * @param float $reward
 * @param int $count
 * @param string $business_account
 * @param string $order_sn
 * @return bool
 */
 function add_order_detail($product_sn, $product_name, $product_attributes, $attributes, $product_price, $integral,
                           $integral_given, $reward, $count, $business_account, $order_sn)
 {
    global $db;

    $order_detail_data = array(
        'product_sn' => $product_sn,
        'product_name' => $product_name,
        'product_attributes' => $product_attributes,
        'attributes' => $attributes,
        'product_price' => $product_price,
        'integral' => $integral,
        'integral_given' => $integral_given,
        'reward' => $reward,
        'count' => $count,
        'business_account' => $business_account,
        'order_sn' => $order_sn
    );

    if($db->autoInsert('order_detail', array($order_detail_data)))
    {
        return true;
    } else {
        return false;
    }
 }

 /**
 * 新增订单
 * @param float $integral_amount
 * @param float $product_amount
 * @param float $delivery_fee
 * @param int $delivery_id
 * @param string $business_account
 * @param float $integral_given_amount
 * @param int $payment_id
 * @param int $address_id
 * @param float $reward_amount
 * @param string $account
 * @param float $integral_paid
 * @param float $reward_paid
 * @param float $balance_paid
 * @param int $status
 * @param bool $self_delivery
 * @return mixed
 */
 function add_order($integral_amount, $product_amount, $delivery_fee, $delivery_id, $business_account, $integral_given_amount,
                    $payment_id, $address_id, $reward_amount, $account, $integral_paid = 0.0, $reward_paid = 0.0, $balance_paid = 0.0, $status = 1, $self_delivery = 0)
 {
    global $db;
    global $config;

    $amount = $product_amount + $delivery_fee - $balance_paid - $reward_paid/$config['reward_rate'] - $integral_paid/$config['integral_rate'];

    $order_data = array(
        'integral_amount' => $integral_amount,
        'product_amount' => $product_amount,
        'delivery_fee' => $delivery_fee,
        'integral_paid' => $integral_paid,
        'reward_paid' => $reward_paid,
        'balance_paid' => $balance_paid,
        'amount' => $amount,
        'business_account' => $business_account,
        'account' => $account,
        'integral_given_amount' => $integral_given_amount,
        'reward_amount' => $reward_amount,
        'self_delivery' => $self_delivery,
        'status' => $status,
        'add_time' => time()
    );

    //获取地址信息
    $get_address = 'select `province`,`city`,`district`,`group`,`address`,`consignee`,`mobile`,`zipcode` from '.
                   $db->table('address').' where `id`='.$address_id.' and `account`=\''.$account.'\'';
    if($address = $db->fetchRow($get_address))
    {
        $order_data['address_id'] = $address_id;
        $order_data['province'] = $address['province'];
        $order_data['city'] = $address['city'];
        $order_data['district'] = $address['district'];
        $order_data['group'] = $address['group'];
        $order_data['address'] = $address['address'];
        $order_data['consignee'] = $address['consignee'];
        $order_data['mobile'] = $address['mobile'];
        $order_data['zipcode'] = $address['zipcode'];
    } else {
        return false;
    }

    //获取物流信息
    $get_delivery = 'select `name` from '.$db->table('delivery').' where `id`='.$delivery_id.' and `business_account`=\''.$business_account.'\'';
    if($delivery = $db->fetchRow($get_delivery))
    {
        $order_data['delivery_id'] = $delivery_id;
        $order_data['delivery_name'] = $delivery['name'];
    } else {
        return false;
    }

    //获取支付方式
    $get_payment = 'select `name` from '.$db->table('payment').' where `id`='.$payment_id;
    if($payment = $db->fetchRow($get_payment))
    {
        $order_data['payment_name'] = $payment['name'];
        $order_data['payment_id'] = $payment_id;
    }  else {
        return false;
    }

    $db->begin();

    $order_sn = '';

    do
    {
        $order_sn = time().rand(1000, 9999);
        $check_order_sn = 'select `id` from '.$db->table('order').' where `order_sn`=\''.$order_sn.'\'';
    } while($db->fetchOne($check_order_sn));

    $order_data['order_sn'] = $order_sn;

    if($db->autoInsert('order', array($order_data)))
    {
        $db->commit();
        return $order_sn;
    }

    $db->rollback();
    return false;
 }

 /**
 * 计算运费
 * @param float $first_weight
 * @param float $next_weight
 * @param float $free
 * @param float $total_weight
 * @return float
 */
 function caculate_delivery_fee($first_weight, $next_weight, $free, $total_weight)
 {
    $delivery_fee = 0;

    if($total_weight >= 1000)
    {
        $delivery_fee += $first_weight;
        $total_weight -= 1000;
    } else {
        $delivery_fee += $first_weight;
        $total_weight = 0;
    }

    $weight_count = intval($total_weight/1000);

    if($total_weight%1000)
    {
        $weight_count++;
    }

    $delivery_fee += $weight_count*$next_weight;

    if($delivery_fee > $free) {
        return $delivery_fee - $free;
    } else {
        return 0;
    }
 }