<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/11
 * Time: 上午9:59
 */
include 'library/init.inc.php';

$operation = 'checkout|caculate_fee|submit_order';

$opera = check_action($operation, getPOST('opera'));

if('submit_order' == $opera)
{
    $address_id = intval(getPOST('address_id'));
    $payment_id = intval(getPOST('payment_id'));
    $use_integral = getPOST('use_integral') == 'true' ? true : false;
    $use_balance = getPOST('use_balance') == 'true' ? true : false;
    $use_reward = getPOST('use_reward') == 'true' ? true : false;

    $response = array('error' => 1, 'msg' => '');

    if(!check_cross_domain())
    {
        $get_address_detail = 'select p.`province_name`,c.`city_name`,d.`district_name`,g.`group_name`,a.`address`,a.`consignee`,'.
            'a.`province`,a.`city`,a.`district`,a.`group`,'.
            'a.`mobile`,a.`zipcode`,a.`id` from '.$db->table('address').' as a, '.$db->table('province').' as p, '.
            $db->table('city').' as c, '.$db->table('district').' as d, '.$db->table('group').' as g where '.
            'a.`province`=p.`id` and a.`city`=c.`id` and a.`district`=d.`id` and a.`group`=g.`id` and a.`id`='.$address_id.
            ' and `account`=\''.$_SESSION['account'].'\'';

        $address_info = $db->fetchRow($get_address_detail);
        //获取待购买产品
        $get_cart_list = 'select p.`reward`,p.`integral_given`,b.`business_account`,c.`checked`,p.`img`,p.`product_type_id`,c.`id`,c.`attributes`,c.`product_sn`,c.`price`,c.`integral`,c.`number`,b.`shop_name`,b.`id` as b_id,p.`name`,p.`weight` from ('.
            $db->table('cart').' as c join '.$db->table('product').' as p using(`product_sn`)) join '.$db->table('business').
            ' as b on (c.`business_account`=b.`business_account`) where c.`account`=\''.$_SESSION['account'].'\' and c.`checked`=1 order by c.`business_account`';

        $cart_list_tmp = $db->fetchAll($get_cart_list);

        $total_amount = 0;
        $total_integral = 0;
        $total_delivery_fee = 0;
        $total_product_amount = 0;
        $total_reward = 0;
        $total_integral_given = 0;

        $cart_list = array();
        foreach($cart_list_tmp as $cart)
        {
            if(!isset($cart_list[$cart['b_id']]))
            {
                $cart_list[$cart['b_id']] = array(
                    'business_account' => $cart['business_account'],
                    'shop_name' => $cart['shop_name'],
                    'products' => array()
                );
            }

            //获取产品属性表
            $get_product_attributes = 'select `id`,`name` from '.$db->table('product_attributes').' where `product_type_id`='.$cart['product_type_id'];
            $attributes_tmp = $db->fetchAll($get_product_attributes);
            $attributes_map = array();
            foreach($attributes_tmp as $a)
            {
                $attributes_map[$a['id']] = $a['name'];
            }

            $attributes = json_decode($cart['attributes']);
            $cart['attributes_str'] = '';
            foreach($attributes as $aid=>$aval)
            {
                $cart['attributes_str'] .= $attributes_map[$aid].':'.$aval.' ';
            }

            //获取产品库存
            $get_inventory = 'select `inventory_logic` from '.$db->table('inventory').' where `product_sn`=\''.$cart['product_sn'].'\' and `attributes`=\''.$cart['attributes'].'\'';
            $inventory = $db->fetchOne($get_inventory);

            $cart_list[$cart['b_id']]['products'][] = array(
                'product_sn' => $cart['product_sn'],
                'product_attributes' => $cart['attributes_str'],
                'count' => intval($cart['number']),
                'product_price' => floatval($cart['price']),
                'integral' => floatval($cart['integral']),
                'integral_given' => floatval($cart['integral_given']),
                'product_name' => $cart['name'],
                'business_account' => $cart['business_account'],
                'reward' => floatval($cart['reward'])
            );

            $total_product_amount += $cart['price'] * $cart['number'];
            $total_integral += $cart['integral'] * $cart['number'];
            $total_integral_given += $cart['integral_given'] * $cart['number'];
        }

        //获取物流信息
        $get_product_weight = 'select b.`id` as b_id, sum(p.`weight`*c.`number`) as `total_weight`,b.`business_account` from '.$db->table('cart').' as c'.
            ' join '.$db->table('product').' as p using(`product_sn`) join '.$db->table('business').' as b '.
            ' on c.`business_account`=b.`business_account` where c.`checked`=1 and c.`account`=\''.$_SESSION['account'].'\' '.
            ' and p.`free_delivery`=0 group by c.`business_account`';
        $product_weight = $db->fetchAll($get_product_weight);

        $get_delivery_sql = 'select da.`first_weight`,da.`next_weight`,da.`free`,da.`delivery_id`,d.`name` from '.$db->table('delivery_area_mapper').
            ' as dam join '.$db->table('delivery_area').' as da on dam.`area_id`=da.`id` join '.$db->table('delivery').
            ' as d on da.`delivery_id`=d.`id` where ';

        foreach($product_weight as $weight)
        {
            $get_delivery = $get_delivery_sql.' dam.`province`='.$address_info['province'].' and dam.`city`='.$address_info['city'].' and dam.`district`='.$address_info['district'];
            $get_delivery .= ' and dam.`business_account`=\''.$weight['business_account'].'\'';
            $get_delivery .= ' union '.$get_delivery_sql.' dam.`province`='.$address_info['province'].' and dam.`city`='.$address_info['city'];
            $get_delivery .= ' and dam.`business_account`=\''.$weight['business_account'].'\'';
            $get_delivery .= ' union '.$get_delivery_sql.' dam.`province`='.$address_info['province'];
            $get_delivery .= ' and dam.`business_account`=\''.$weight['business_account'].'\'';

            $delivery_list = $db->fetchAll($get_delivery);

            $delivery_list_mapper = array();
            foreach($delivery_list as $delivery)
            {
                $tmp = array(
                    'delivery_id' => $delivery['delivery_id'],
                    'delivery_name' => $delivery['name'],
                    'delivery_fee' => caculate_delivery_fee($delivery['first_weight'], $delivery['next_weight'], $delivery['free'], $weight['total_weight'])
                );

                $delivery_list_mapper[] = $tmp;
            }
            $cart_list[$weight['b_id']]['delivery_list'] = $delivery_list_mapper;
            $cart_list[$weight['b_id']]['delivery_fee'] = $delivery_list_mapper[0]['delivery_fee'];
            $cart_list[$weight['b_id']]['delivery_id'] = $delivery_list_mapper[0]['delivery_id'];
        }

        //读取用户信息
        $get_user_info = 'select `integral`,`reward`,`balance` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
        $user_info = $db->fetchRow($get_user_info);

        $total_amount = $_SESSION['total_amount'];
        $integral_paid = 0;
        $reward_paid = 0;
        $balance_paid = 0;
        if($use_integral && $total_amount > 0)
        {
            if($total_amount >= $user_info['integral']/$config['integral_rate'])
            {
                $total_amount -= $user_info['integral']/$config['integral_rate'];
                $integral_paid = $user_info['integral'];
            } else {
                $integral_paid = $total_amount * $config['integral_rate'];
                $total_amount = 0;
            }
        }

        if($use_reward && $total_amount > 0)
        {
            if($total_amount >= $user_info['reward']/$config['reward_rate'])
            {
                $total_amount -= $user_info['reward']/$config['reward_rate'];
                $reward_paid = $user_info['reward'];
            } else {
                $reward_paid = $total_amount * $config['reward_rate'];
                $total_amount = 0;
            }
        }

        if($use_balance && $total_amount > 0)
        {
            if($total_amount >= $user_info['balance'])
            {
                $total_amount -= $user_info['balance'];
                $balance_paid = $user_info['balance'];
            } else {
                $balance_paid = $total_amount;
                $total_amount = 0;
            }
        }

        $response['msg'] = '订单提交功能正在建设中...';
        //插入订单
        foreach($cart_list as $cart)
        {
            $status = 1;
            if($total_amount == 0)
            {
                $status = 3;
            }
            $delivery_id = $cart['delivery_id'];
            $business_account = $cart['business_account'];
            $total_delivery_fee = $cart['delivery_fee'];
            $order_sn = add_order($total_integral, $total_product_amount, $total_delivery_fee, $delivery_id,
                                  $business_account, $total_integral_given, $payment_id, $address_id, $total_reward,
                                  $_SESSION['account'], $integral_paid, $reward_paid, $balance_paid, $status);

            if($order_sn)
            {
                if($status == 3)
                {
                    add_order_log($order_sn, $_SESSION['account'], $status, "使用余额/圈币/积分支付");
                }

                if($balance_paid || $reward_paid || $integral_paid)
                {
                    add_memeber_exchange_log($_SESSION['account'], -1 * $balance_paid, -1 * $reward_paid, -1 * $integral_paid, 0, 0, $_SESSION['account'], "抵扣订单金额");
                }
                $flag = true;
                foreach($cart['products'] as $od)
                {
                    if(!add_order_detail($od['product_sn'], $od['product_name'], $od['product_attributes'], $od['product_price'], $od['integral'], $od['integral_given'], $od['reward'], $od['count'], $business_account, $order_sn))
                    {
                        $flag = false;
                    }
                }

                if($flag) {
                    $response['error'] = 0;
                    $_SESSION['order_sn'] = $order_sn;
                    $response['status'] = $status;
                } else {
                    $response['msg'] = '提交订单信息失败，请稍后再试';
                }
            } else {
                $response['msg'] = '订单提交失败，请稍后再试';
            }
        }
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}

if('caculate_fee' == $opera)
{
    $use_integral = getPOST('use_integral') == 'true' ? true : false;
    $use_balance = getPOST('use_balance') == 'true' ? true : false;
    $use_reward = getPOST('use_reward') == 'true' ? true : false;

    $response = array('error' => 1, 'msg' => '');

    if(!check_cross_domain())
    {
        //读取用户信息
        $get_user_info = 'select `integral`,`reward`,`balance` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
        $user_info = $db->fetchRow($get_user_info);

        $total_amount = $_SESSION['total_amount'];
        if($use_integral && $total_amount > 0)
        {
            if($total_amount >= $user_info['integral']/$config['integral_rate'])
            {
                $total_amount -= $user_info['integral']/$config['integral_rate'];
            } else {
                $total_amount = 0;
            }
        }

        if($use_reward && $total_amount > 0)
        {
            if($total_amount >= $user_info['reward']/$config['reward_rate'])
            {
                $total_amount -= $user_info['reward']/$config['reward_rate'];
            } else {
                $total_amount = 0;
            }
        }

        if($use_balance && $total_amount > 0)
        {
            if($total_amount >= $user_info['balance'])
            {
                $total_amount -= $user_info['balance'];
            } else {
                $total_amount = 0;
            }
        }

        $response['error'] = 0;
        $response['total_amount'] = $total_amount;
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}

if('checkout' == $opera)
{
    $cart = getPOST('cart');

    $response = array('error'=>1, 'msg'=>'');

    if(!check_cross_domain()) {
        //过滤要购买的产品
        $buy_number = 0;
        $cart_data = array();
        foreach ($cart as $c)
        {
            if ($c['number'] > 0 && $c['checked'])
            {
                $buy_number += intval($c['number']);
            }

            $log->record_array($c);

            $cart_data[] = array(
                'id' => intval($c['c_id']),
                'number' => intval($c['number']),
                'checked' => $c['checked'] == 'true' ? 1 : 0
            );
        }

        if($buy_number == 0)
        {
            $response['msg'] = '请选择要购买的产品';
        } else {
            //更新购物车数据
            $db->begin();

            $commit = true;
            foreach($cart_data as $cd)
            {
                $data = array('number'=>$cd['number'], 'checked'=>$cd['checked']);

                if(!$db->autoUpdate('cart', $data, '`id`='.$cd['id'].' and `account`=\''.$_SESSION['account'].'\''))
                {
                    $db->rollback();
                    $responsep['msg'] = '000:系统繁忙，请稍后再试';
                    $commit = false;
                    break;
                }
            }

            if($commit)
            {
                $db->commit();
                //检查库存
                $get_inventory_list = 'select c.`id`,c.`number`,i.`inventory_logic` from '.$db->table('cart').' as c join '.
                                 $db->table('inventory').' as i on c.`product_sn`=i.`product_sn` and c.`attributes`=i.`attributes`'.
                                 ' where c.`account`=\''.$_SESSION['account'].'\' and c.`checked`=1';

                $inventory_list = $db->fetchAll($get_inventory_list);
                $success = true;
                foreach($inventory_list as $inventory)
                {
                    if($inventory['inventory_logic'] < $inventory['number'])
                    {
                        $success = false;
                        $data = array('number'=>$inventory['inventory_logic']);

                        $db->autoUpdate('cart', $data, '`id`='.$inventory['id']);
                    }
                }

                if($success)
                {
                    $response['error'] = 0;
                    $response['msg'] = '正在进入结算页面';
                    $response['refresh'] = false;
                } else {
                    $response['msg'] = '部分产品库存不足';
                    $response['refresh'] = true;
                }
            }
        }
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}

//检查如果没有绑定手机号码的话，先绑定一个
$check_user_mobile = 'select `mobile` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$mobile = $db->fetchOne($check_user_mobile);

if(!$mobile)
{
    redirect('bind_mobile.php');
}

//检查如果没有默认地址的话，先设置一个
$check_address_id = 'select `id` from '.$db->table('address').' where `account`=\''.$_SESSION['account'].'\' and `is_default`=1';
$address_id = $db->fetchOne($check_address_id);

if(!$address_id)
{
    redirect('address.php?act=add');
}

//读取地址信息
if(isset($_SESSION['address_id']))
{
    $address_id = $_SESSION['address_id'];
} else {
    $_SESSION['address_id'] = $address_id;
}

$get_address_detail = 'select p.`province_name`,c.`city_name`,d.`district_name`,g.`group_name`,a.`address`,a.`consignee`,'.
                      'a.`province`,a.`city`,a.`district`,a.`group`,'.
                      'a.`mobile`,a.`zipcode`,a.`id` from '.$db->table('address').' as a, '.$db->table('province').' as p, '.
                      $db->table('city').' as c, '.$db->table('district').' as d, '.$db->table('group').' as g where '.
                      'a.`province`=p.`id` and a.`city`=c.`id` and a.`district`=d.`id` and a.`group`=g.`id` and a.`id`='.$address_id.
                      ' and `account`=\''.$_SESSION['account'].'\'';

$address_info = $db->fetchRow($get_address_detail);
assign('address_info', $address_info);

//获取待购买产品
$get_cart_list = 'select c.`checked`,p.`img`,p.`product_type_id`,c.`id`,c.`attributes`,c.`product_sn`,c.`price`,c.`integral`,c.`number`,b.`shop_name`,b.`id` as b_id,p.`name`,p.`weight` from ('.
    $db->table('cart').' as c join '.$db->table('product').' as p using(`product_sn`)) join '.$db->table('business').
    ' as b on (c.`business_account`=b.`business_account`) where c.`account`=\''.$_SESSION['account'].'\' and c.`checked`=1 order by c.`business_account`';

$cart_list_tmp = $db->fetchAll($get_cart_list);

$total_amount = 0;
$total_integral = 0;
$total_delivery_fee = 0;
$total_product_amount = 0;

$cart_list = array();
foreach($cart_list_tmp as $cart)
{
    if(!isset($cart_list[$cart['b_id']]))
    {
        $cart_list[$cart['b_id']] = array(
            'b_id' => $cart['b_id'],
            'shop_name' => $cart['shop_name'],
            'products' => array()
        );
    }

    //获取产品属性表
    $get_product_attributes = 'select `id`,`name` from '.$db->table('product_attributes').' where `product_type_id`='.$cart['product_type_id'];
    $attributes_tmp = $db->fetchAll($get_product_attributes);
    $attributes_map = array();
    foreach($attributes_tmp as $a)
    {
        $attributes_map[$a['id']] = $a['name'];
    }

    $attributes = json_decode($cart['attributes']);
    $cart['attributes_str'] = '';
    foreach($attributes as $aid=>$aval)
    {
        $cart['attributes_str'] .= $attributes_map[$aid].':'.$aval.' ';
    }

    $cart_list[$cart['b_id']]['products'][] = array(
        'c_id' => $cart['id'],
        'product_sn' => $cart['product_sn'],
        'attributes' => $cart['attributes_str'],
        'number' => intval($cart['number']),
        'price' => floatval($cart['price']),
        'integral' => floatval($cart['integral']),
        'name' => $cart['name'],
        'img' => $cart['img']
    );

    $total_product_amount += $cart['price'] * $cart['number'];
    $total_integral += $cart['integral'] * $cart['number'];
}

//获取物流信息
$get_product_weight = 'select b.`id` as b_id, sum(p.`weight`*c.`number`) as `total_weight`,b.`business_account` from '.$db->table('cart').' as c'.
                      ' join '.$db->table('product').' as p using(`product_sn`) join '.$db->table('business').' as b '.
                      ' on c.`business_account`=b.`business_account` where c.`checked`=1 and c.`account`=\''.$_SESSION['account'].'\' '.
                      ' and p.`free_delivery`=0 group by c.`business_account`';
$product_weight = $db->fetchAll($get_product_weight);

$get_delivery_sql = 'select da.`first_weight`,da.`next_weight`,da.`free`,da.`delivery_id`,d.`name` from '.$db->table('delivery_area_mapper').
                    ' as dam join '.$db->table('delivery_area').' as da on dam.`area_id`=da.`id` join '.$db->table('delivery').
                    ' as d on da.`delivery_id`=d.`id` where ';

$delivery_list_json = array();
foreach($product_weight as $weight)
{
    $get_delivery = $get_delivery_sql.' dam.`province`='.$address_info['province'].' and dam.`city`='.$address_info['city'].' and dam.`district`='.$address_info['district'];
    $get_delivery .= ' and dam.`business_account`=\''.$weight['business_account'].'\'';
    $get_delivery .= ' union '.$get_delivery_sql.' dam.`province`='.$address_info['province'].' and dam.`city`='.$address_info['city'];
    $get_delivery .= ' and dam.`business_account`=\''.$weight['business_account'].'\'';
    $get_delivery .= ' union '.$get_delivery_sql.' dam.`province`='.$address_info['province'];
    $get_delivery .= ' and dam.`business_account`=\''.$weight['business_account'].'\'';

    $delivery_list = $db->fetchAll($get_delivery);

    $delivery_list_mapper = array();
    foreach($delivery_list as $delivery)
    {
        $tmp = array(
            'delivery_id' => $delivery['delivery_id'],
            'delivery_name' => $delivery['name'],
            'delivery_fee' => caculate_delivery_fee($delivery['first_weight'], $delivery['next_weight'], $delivery['free'], $weight['total_weight'])
        );

        $delivery_list_mapper[] = $tmp;
        $delivery_list_json[] = $tmp;
    }
    $cart_list[$weight['b_id']]['delivery_list'] = $delivery_list_mapper;
}

//把运费计入总金额
foreach($cart_list as $cart)
{
    $total_delivery_fee += $cart['delivery_list'][0]['delivery_fee'];
}
$total_amount = $total_product_amount + $total_delivery_fee;
$_SESSION['total_amount'] = $total_amount;

//读取用户信息
$get_user_info = 'select `integral`,`reward`,`balance` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$user_info = $db->fetchRow($get_user_info);

assign('user_info', $user_info);
assign('delivery_list_json', json_encode($delivery_list_json));
assign('total_amount', $total_amount);
assign('total_product_amount', $total_product_amount);
assign('total_delivery_fee', $total_delivery_fee);
assign('total_integral', $total_integral);
assign('total_number', count($cart_list_tmp));
assign('cart_list', $cart_list);

$smarty->display('checkout.phtml');