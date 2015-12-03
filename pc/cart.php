<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/10
 * Time: 下午5:11
 */
include 'library/init.inc.php';

$operation = 'add_to_cart|delete|empty_cart|multi_delete|update_cart';

$opera = check_action($operation, getPOST('opera'));

if( 'update_cart' == $opera ) {
    $response = array('error'=>1, 'msg'=>'');

    if(!check_cross_domain() && isset($_SESSION['account']) && $_SESSION['account']) {
        $get_cart_list = 'select c.price, c.integral, c.number, c.attributes, p.id, p.img, p.name, c.id as cid from '.$db->table('cart').' as c ';
        $get_cart_list .= ' left join '.$db->table('product').' as p on c.product_sn = p.product_sn';
        $get_cart_list .= ' where c.account = \''.$_SESSION['account'].'\' and c.checked = 1';

        $cart_list = $db->fetchAll($get_cart_list);
        $cart_price_amount = 0;
        if( $cart_list ) {
            foreach( $cart_list as $key => $cart ) {
                if( empty($cart['attributes']) ) {
                    $cart_list[$key]['attributes_str'] = array();
                } else {
                    $cart_list[$key]['attributes_str'] = json_decode($cart['attributes']);
                }
                $cart_price_amount += ($cart['price'] * $cart['number']);
            }
        }
        assign('cart_price_amount', $cart_price_amount);
        assign('mini_cart_list', $cart_list);
        $response['error'] = 0;
        $response['content'] = $smarty->fetch('cart-item.phtml');

    } else {
        if(empty($_SESSION['account']))
        {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }

    echo json_encode($response);
    exit;
}

if('delete' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $cid = intval(getPOST('cid'));

    if(!check_cross_domain())
    {
        if($cid <= 0)
        {
            $response['msg'] = '403:参数错误';
        }

        if($response['msg'] == '')
        {
            if($db->autoDelete('cart', '`id`='.$cid.' and `account`=\''.$_SESSION['account'].'\''))
            {
                $response['error'] = 0;
                $response['msg'] = '产品已移出购物车';
            } else {
                $response['msg'] = '001:系统繁忙，请稍后再试';
            }
        }
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}

if('add_to_cart' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $product_sn = getPOST('product_sn');
    $number = intval(getPOST('number'));
    $attributes = getPOST('attributes');

    if(!check_cross_domain() && isset($_SESSION['account']) && $_SESSION['account'])
    {
        if ($product_sn == '')
        {
            $response['msg'] .= "-参数错误\n";
        } else {
            $product_sn = $db->escape($product_sn);
        }

        if($number <= 0)
        {
            $response['msg'] .= "-请输入购买数量\n";
        }

        if($attributes != '' && count($attributes) > 0)
        {
            $attributes_tmp = $attributes;
            $attributes = '{';
            foreach($attributes_tmp as $id=>$value)
            {
                $attributes .= '"'.$id.'":"'.$db->escape($value).'",';
            }
            $attributes = substr($attributes, 0, strlen($attributes)-1);
            $attributes .= '}';
            $attributes = $db->escape($attributes);
        } else {
            $attributes = '';
        }

        if($response['msg'] == '') {
            $check_cart = 'select `id`,`number` from ' . $db->table('cart') .
                          ' where `product_sn`=\'' . $product_sn . '\' and `account`=\'' . $_SESSION['account'] . '\' '.
                          ' and `attributes`=\''.$attributes.'\'';
            $cart = $db->fetchRow($check_cart);

            $buy_number = $number;

            if($cart)
            {
                $buy_number += $cart['number'];
            }

            //检查库存
            $check_inventory = 'select `inventory_logic` from '.$db->table('inventory').
                               ' where `attributes`=\''.$attributes.'\' and `product_sn`=\''.$product_sn.'\'';

            $log->record($check_inventory);
            $inventory_logic = $db->fetchOne($check_inventory);
            $response['inventory_logic'] = $inventory_logic;
            if($inventory_logic < $buy_number)
            {
                $buy_number = $inventory_logic;
            }

            if( $inventory_logic >= $buy_number && $inventory_logic > 0 )
            {
                //获取产品信息
                $get_product = 'select `price`,`integral`,`business_account`,`is_virtual`,`promote_price`,`promote_begin`,`promote_end` from '.$db->table('product').
                               ' where `product_sn`=\''.$product_sn.'\'';
                $product = $db->fetchRow($get_product);
                $now = time();

                //获取产品砍价总额
                $get_product_discount = 'select sum(`reduce`) from '.$db->table('discount').
                                        ' where `product_sn`=\''.$product_sn.'\' and `owner`=\''.$_SESSION['account'].'\'';

                $discount = $db->fetchOne($get_product_discount);

                if($cart)
                {
                    //更新数量
                    $cart_data = array(
                        'number' => $buy_number,
                        'price' => $product['price'] - $discount,
                        'integral' => $product['integral']
                    );

                    if($product['promote_end'] > $now && $product['promote_begin'] <= $now)
                    {
                        $cart_data['price'] = $product['promote_price'];
                    }

                    if($db->autoUpdate('cart', $cart_data, '`id`='.$cart['id']))
                    {
                        $response['error'] = 0;
                        $response['msg'] = '加入购物车成功';
                        $response['add_number'] = $buy_number - $cart['number'];
                    } else {
                        $response['msg'] = '001:系统繁忙，请稍后再试';
                    }
                } else {
                    //新增记录
                    $cart_data = array(
                        'openid' => $_SESSION['openid'],
                        'account' => $_SESSION['account'],
                        'product_sn' => $product_sn,
                        'number' => $buy_number,
                        'add_time' => time(),
                        'price' => $product['price'] - $discount,
                        'integral' => $product['integral'],
                        'business_account' => $product['business_account'],
                        'attributes' => $attributes,
                        'is_virtual' => $product['is_virtual']
                    );

                    if($product['promote_end'] > $now && $product['promote_begin'] <= $now)
                    {
                        $cart_data['price'] = $product['promote_price'];
                    }

                    if($db->autoInsert('cart', array($cart_data)))
                    {
                        $response['error'] = 0;
                        $response['msg'] = '加入购物车成功';
                        $response['add_number'] = $buy_number;
                    } else {
                        $response['msg'] = '001:系统繁忙，请稍后再试';
                    }
                }
            } else {
                $response['msg'] = '产品库存不足';
            }
        }
    } else {
        if(empty($_SESSION['account']))
        {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }

    echo json_encode($response);
    exit;
}

if( 'empty_cart' == $opera ) {
    $response = array('error'=>1, 'msg'=>'');

    if(!check_cross_domain())
    {
        if($response['msg'] == '')
        {
            if($db->autoDelete('cart', '`account`=\''.$_SESSION['account'].'\''))
            {
                $response['error'] = 0;
                $response['msg'] = '购物车已清空';
            } else {
                $response['msg'] = '001:系统繁忙，请稍后再试';
            }
        }
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}

if( 'multi_delete' == $opera ) {
    $response = array('error'=>1, 'msg'=>'');

    $cid_list = getPOST('cid_list');
    $cid_list = json_decode($cid_list);
    if(!check_cross_domain())
    {
        if(!is_array($cid_list))
        {
            $response['msg'] = '403:参数错误';
        }

        if($response['msg'] == '')
        {
            foreach( $cid_list as $key => $cid ) {
                $cid_list[$key] = intval($cid);
            }
            $condition = implode(',', $cid_list);
            $multi_delete = 'delete from '.$db->table('cart').' where `account`=\''.$_SESSION['account'].'\' and `id` in ('.$cid_list.')';
            if($db->autoDelete('cart', '`id`='.$cid.' and `account`=\''.$_SESSION['account'].'\''))
            {
                $response['error'] = 0;
                $response['msg'] = '产品已移出购物车';
            } else {
                $response['msg'] = '001:系统繁忙，请稍后再试';
            }
        }
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}
//子查询是否已收藏
$sub_select = '(select `add_time` from '.$db->table('collection').' where `product_sn` = c.`product_sn` and `account` = \''.$_SESSION['account'].'\') as collection';

//获取购物车产品
$get_cart_list = 'select c.`checked`,p.`img`,p.`product_type_id`,c.`id`,c.`attributes`,c.`product_sn`,c.`price`,c.`integral`,c.`number`,b.`shop_name`,b.`id` as b_id,p.`name`,p.`id` as p_id,c.`business_account`,p.`free_delivery`,p.`weight`,p.`is_virtual`,'.$sub_select.' from ('.
                 $db->table('cart').' as c join '.$db->table('product').' as p using(`product_sn`)) join '.$db->table('business').
                 ' as b on (c.`business_account`=b.`business_account`) where c.`account`=\''.$_SESSION['account'].'\' order by c.`business_account`';
$cart_list_tmp = $db->fetchAll($get_cart_list);

$total_amount = 0;
$total_integral = 0;
$total_delivery_fee = 0;
$total_number = 0;

$cart_list = array();
$cart_json = array();
$check_all = false;
$cart_weight_json = array();

if($cart_list_tmp)
{

    foreach ($cart_list_tmp as $cart)
    {
        if (!isset($cart_list[$cart['b_id']]))
        {
            $cart_list[$cart['b_id']] = array(
                'b_id' => $cart['b_id'],
                'shop_name' => $cart['shop_name'],
                'products' => array(),
                'business_account' => $cart['business_account'],
                'delivery_fee' => 0,
                'weight' => 0,
                'free_delivery' => true,
            );
        }
        //店铺选中
        $cart_list[$cart['b_id']]['checked'] = $cart['checked'] ? true : false;
        //全选选中
        $check_all = !$cart['checked'] ? false : true;

        $total_number += $cart['checked'] ? $cart['number'] : 0;

        $cart_list[$cart['b_id']]['weight'] += ($cart['checked'] == 0 || $cart['free_delivery'] == 1 || $cart['is_virtual'] == 1) ? 0 : ($cart['weight'] * $cart['number']);
        $cart['weight'] = ($cart['checked'] == 0 || $cart['free_delivery'] == 1 || $cart['is_virtual'] == 1) ? 0 : $cart['weight'];
        //获取产品属性表
        $get_product_attributes = 'select `id`,`name` from ' . $db->table('product_attributes') . ' where `product_type_id`=' . $cart['product_type_id'];
        $attributes_tmp = $db->fetchAll($get_product_attributes);
        $attributes_map = array();
        if($attributes_tmp)
        {
            foreach ($attributes_tmp as $a)
            {
                $attributes_map[$a['id']] = $a['name'];
            }
        } else {
            $attributes_map = '';
        }

        $attributes = json_decode($cart['attributes']);
        $cart['attributes_str'] = '';
        if($attributes)
        {
            foreach ($attributes as $aid => $aval)
            {
                $cart['attributes_str'] .= $attributes_map[$aid] . ':' . $aval . ' ';
            }
        }

        //获取产品库存
        $get_inventory = 'select `inventory_logic` from '.$db->table('inventory').' where `product_sn`=\''.$cart['product_sn'].'\' and `attributes`=\''.$cart['attributes'].'\'';
        $inventory = $db->fetchOne($get_inventory);

        $cart_list[$cart['b_id']]['products'][] = array(
            'c_id' => $cart['id'],
            'product_sn' => $cart['product_sn'],
            'attributes' => $cart['attributes_str'],
            'number' => intval($cart['number']),
            'price' => floatval($cart['price']),
            'integral' => floatval($cart['integral']),
            'name' => $cart['name'],
            'img' => $cart['img'],
            'checked' => $cart['checked'],
            'inventory' => $inventory,
            'p_id' => $cart['p_id'],
            'free_delivery' => $cart['free_delivery'],
            'weight' => $cart['weight'],
            'collection' => $cart['collection'],
        );

        $cart_json[$cart['id']] = array(
            'c_id' => $cart['id'],
            'product_sn' => $cart['product_sn'],
            'number' => intval($cart['number']),
            'price' => floatval($cart['price']),
            'integral' => floatval($cart['integral']),
            'b_id' => $cart['b_id'],
            'checked' => $cart['checked'],
            'inventory' => intval($inventory),
            'p_id' => $cart['p_id'],
            'weight' => $cart['weight'],
            'free_delivery' => $cart['free_delivery'],
        );
        $cart_json[$cart['id']]['free_delivery'] = ($cart['free_delivery'] == 1 || $cart['is_virtual'] == 1) ? true : false;
        $cart_list[$cart['b_id']]['free_delivery'] = $cart_list[$cart['b_id']]['free_delivery'] && ($cart['free_delivery'] == 1 || $cart['is_virtual'] == 1) ? true : false;
        if ($cart['checked'])
        {
            $total_amount += $cart['price'] * $cart['number'];
            $total_integral += $cart['integral'] * $cart['number'];
        }
    }
    //计算运费
    foreach( $cart_list as $key => $cart ) {

        $cart_weight_json[$cart['b_id']] = array(
            'b_id' => $cart['b_id'],
            'weight' => $cart['weight'],
            'free_delivery' => true,
        );

        //物流信息
        $get_delivery_area = 'select * from '.$db->table('delivery_area');
        $get_delivery_area .= ' where business_account = \''.$cart['business_account'].'\'';
        $delivery_area = $db->fetchAll($get_delivery_area);
        $delivery_fee = 65535;
        $target_area = '';
        //最小物流费用
        if( $delivery_area ) {
            foreach( $delivery_area as $area ) {
                $temp = caculate_delivery_fee($area['first_weight'], $area['next_weight'], $area['free'], $cart['weight']);
                if( $delivery_fee > $temp ) {
                    $delivery_fee = $temp;
                    $target_area = $area;
                }
            }
        }
        $cart_weight_json[$cart['b_id']]['first_weight'] = $area['first_weight'];
        $cart_weight_json[$cart['b_id']]['next_weight'] = $area['next_weight'];
        $cart_weight_json[$cart['b_id']]['free'] = $area['free'];
        if( $cart['free_delivery'] ) {
            $cart_list[$key]['delivery_fee'] = 0;
        } else {
            $cart_list[$key]['delivery_fee'] = caculate_delivery_fee($area['first_weight'], $area['next_weight'], $area['free'], $cart['weight']);
        }
        $total_delivery_fee += $cart_list[$key]['delivery_fee'];
    }
}
assign('total_delivery_fee', $total_delivery_fee);
assign('check_all', $check_all);
assign('total_amount', $total_amount);
assign('total_integral', $total_integral);
assign('total_number', $total_number);
assign('cart_list', $cart_list);
assign('cart_json', json_encode($cart_json));
assign('cart_weight_json', json_encode($cart_weight_json));
$smarty->display('cart.phtml');
