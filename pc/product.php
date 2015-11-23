<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/7
 * Time: 上午11:22
 */
include 'library/init.inc.php';

$operation = 'collection|distribution|delete_history|delivery_city|delivery_district|empty_history|get_comments|add_to_cart';
$opera = check_action($operation, getPOST('opera'));

$page_count = 1;    //一页评论的数量，调试多个页面
//获取评论
if( 'get_comments' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    $type_array = array('all', 'good', 'normal', 'bad');
    if(!check_cross_domain()) {
        $product_sn = trim(getPOST('product_sn'));
        $type = trim(getPOST('type'));
        $page = intval(getPOST('page'));

        $product_sn = $db->escape($product_sn);

        if( $product_sn == '' ) {
            $response['msg'] = '参数错误';
            echo json_encode($response);
            exit;
        }

        $type = in_array($type, $type_array) ? $type : 'all';

        $where = ' c.parent_id = 0';
        switch($type) {
            case 'all': $where .= '';break;
            case 'good': $where .= ' and c.star = 5';break;
            case 'normal': $where .= ' and c.star > 1 and c.star < 5';break;
            case 'bad': $where .= ' and c.star = 1';break;
            default: $where .= '';break;
        }
        $where .= ' and product_sn = \''.$product_sn.'\'';

        $get_total = 'select count(*) from '.$db->table('comment').' as c where '.$where;
        $total = $db->fetchOne($get_total);
        $total_page = ceil($total / $page_count);

        $offset = $page_count * ( $page - 1 );
        $page = $page > $total_page ? $total_page : $page;
        $page = $page < 1 ? 1 : $page;
        $offset = $page_count * ( $page - 1 );

        //读取评论信息
        $get_comments = 'select c.`id`,c.`comment`,c.`star`,c.`add_time`,m.`headimg`,m.`nickname` from '.$db->table('comment').' as c'.
            ' join '.$db->table('member').' as m using(`account`) where '.$where.' order by c.add_time desc limit '.$offset.','.$page_count;
        $comments = $db->fetchAll($get_comments);
        if( $comments ) {
            foreach($comments as $key=>$c)
            {
                if( empty($c['headimg']) ) {
                    $comments[$key]['headimg'] = 'images/default-user.png';
                }
                $comments[$key]['add_time'] = date('Y/m/d H:i:s', $c['add_time']);
                $get_reply = 'select `comment`,`add_time` from '.$db->table('comment').' where `parent_id`='.$c['id'];

                $reply = $db->fetchRow($get_reply);
                if( $reply ) {
                    $comments[$key]['reply'] = $reply;
                    $comments[$key]['reply']['add_time'] = date('Y/m/d H:i:s', $comments[$key]['reply']['add_time']);
                }
            }

            $response['error'] = 0;
            $response['comments'] = $comments;
            $response['total_page'] = $total_page;
        } else {
//            $response['comments'] = $get_comments;
            $response['msg'] = '参数错误';
        }
        $response['type'] = $type;
        $response['page'] = $page;

    } else {
        $response['msg'] = '请从本站提交数据';
    }
    echo json_encode($response);
    exit;
}
//清空我的足迹
if( 'empty_history' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    if(!check_cross_domain() && !empty($_SESSION['account'])) {
        $delete_history = 'delete from '.$db->table('history').' where `account`=\''.$_SESSION['account'].'\'';
        if($db->delete($delete_history))
        {
            $response['error'] = 0;
            $response['msg'] = '删除足迹成功';
        } else {
            $response['msg'] = '001:系统繁忙，请稍后再试';
        }
    } else {
        if (empty($_SESSION['account'])) {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }
    echo json_encode($response);
    exit;
}
//我的足迹
if('delete_history' == $opera)
{
    $product_sn = getPOST('product_sn');

    $response = array('error'=>1, 'msg'=>'');

    if(!check_cross_domain() && !empty($_SESSION['account']))
    {
        if($product_sn != '')
        {
            $product_sn = $db->escape($product_sn);

            $delete_history = 'delete from '.$db->table('history').' where `account`=\''.$_SESSION['account'].'\' and `product_sn`=\''.$product_sn.'\'';
            if($db->delete($delete_history))
            {
                $response['error'] = 0;
                $response['msg'] = '删除足迹成功';
            } else {
                $response['msg'] = '001:系统繁忙，请稍后再试';
            }
        } else {
            $response['msg'] = '000:参数错误';
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
//产品分销
if('distribution' == $opera)
{
    $product_sn = getPOST('product_sn');

    $response = array('error'=>1, 'msg'=>'');

    if(!check_cross_domain() && !empty($_SESSION['account']))
    {
        if($product_sn != '')
        {
            $product_sn = $db->escape($product_sn);
            //检查产品的收藏状态
            $get_distribution = 'select `product_sn` from '.$db->table('distribution').
                ' where `account`=\''.$_SESSION['account'].'\' and `product_sn`=\''.$product_sn.'\'';
            $distribution_flag = $db->fetchOne($get_distribution) ? true : false;

            if($distribution_flag)
            {
                if(cancel_distribution_product($_SESSION['account'], $product_sn))
                {
                    $response['error'] = 0;
                    $response['status'] = !$distribution_flag;
                    $response['msg'] = '取消分销成功';
                } else {
                    $response['msg'] = '001:系统繁忙，请稍后再试';
                }
            } else {
                if(distribution_product($_SESSION['account'], $product_sn))
                {
                    $response['error'] = 0;
                    $response['status'] = !$distribution_flag;
                    $response['msg'] = '分销产品成功';
                } else {
                    $response['msg'] = '001:系统繁忙，请稍后再试';
                }
            }
        } else {
            $response['msg'] = '000:参数错误';
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
//产品收藏
if('collection' == $opera)
{
    $product_sn = getPOST('product_sn');

    $response = array('error'=>1, 'msg'=>'');

    if(!check_cross_domain() && !empty($_SESSION['account']))
    {
        if($product_sn != '')
        {
            $product_sn = $db->escape($product_sn);
            //检查产品的收藏状态
            $get_collection = 'select `product_sn` from '.$db->table('collection').
                              ' where `account`=\''.$_SESSION['account'].'\' and `product_sn`=\''.$product_sn.'\'';
            $collection_flag = $db->fetchOne($get_collection) ? true : false;

            if($collection_flag)
            {
                if(cancel_collection_product($_SESSION['account'], $product_sn))
                {
                    $response['error'] = 0;
                    $response['status'] = !$collection_flag;
                    $response['product_sn'] = $product_sn;
                    $response['msg'] = '取消收藏成功';
                } else {
                    $response['msg'] = '001:系统繁忙，请稍后再试';
                }
            } else {
                if(collection_product($_SESSION['account'], $product_sn))
                {
                    $response['error'] = 0;
                    $response['status'] = !$collection_flag;
                    $response['product_sn'] = $product_sn;
                    $response['msg'] = '收藏产品成功';
                } else {
                    $response['msg'] = '001:系统繁忙，请稍后再试';
                }
            }
        } else {
            $response['msg'] = '000:参数错误';
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
//获取配送地址-城市
if( 'delivery_city' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );

    if( check_cross_domain() ) {
        $response['msg'] = '请从本站提交';
        echo json_encode($response);
        exit;
    }
    $product_sn = trim(getPOST('product_sn'));
    $id = intval(getPOST('id'));

    $product_sn = $db->escape($product_sn);
    $table = 'city';

    $get_city_list = 'select m.city, t.id, t.city_name from '.$db->table('delivery_area_mapper').' as m';
    $get_city_list .= ' left join '.$db->table($table).' as t on t.id = m.city';
    $get_city_list .= ' left join '.$db->table('product').' as p on p.business_account = m.business_account';
    $get_city_list .= ' where p.product_sn = \''.$product_sn.'\' and m.province = \''.$id.'\'';

    $city_list = $db->fetchAll($get_city_list);
    if( $city_list ) {
        $useful = false;
        foreach( $city_list as $key => $city ) {
            if( $city['city'] != 0 ) {
                $useful = true;
            } else {
                unset($city_list[$key]);
            }
        }
        if( !$useful ) {
            $get_city_list = 'select id, city_name from '.$db->table('city').' where province_id = \''.$id.'\'';
            $city_list = $db->fetchAll($get_city_list);
        }

        $response['error'] = 0;
        $response['data'] = $city_list;
    } else {
        $response['msg'] = '系统繁忙，请稍后重试';
    }
    echo json_encode($response);
    exit;
}
//获取配送地址-区
if( 'delivery_district' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );

    if( check_cross_domain() ) {
        $response['msg'] = '请从本站提交';
        echo json_encode($response);
        exit;
    }
    $product_sn = trim(getPOST('product_sn'));
    $id = intval(getPOST('id'));

    $product_sn = $db->escape($product_sn);
    $table = 'district';

    $get_district_list = 'select m.district, t.id, t.district_name from '.$db->table('delivery_area_mapper').' as m';
    $get_district_list .= ' left join '.$db->table($table).' as t on t.id = m.district';
    $get_district_list .= ' left join '.$db->table('product').' as p on p.business_account = m.business_account';
    $get_district_list .= ' where p.product_sn = \''.$product_sn.'\' and m.city = \''.$id.'\'';

    $district_list = $db->fetchAll($get_district_list);
    if( $district_list ) {
        $useful = false;
        foreach( $district_list as $key => $district ) {
            if( $district['district'] != 0 ) {
                $useful = true;
            } else {
                unset($district_list[$key]);
            }
        }
        if( !$useful ) {
            $get_district_list = 'select id, district_name from '.$db->table('district').' where city_id = \''.$id.'\'';
            $district_list = $db->fetchAll($get_district_list);
        }

        $response['error'] = 0;
        $response['data'] = $district_list;
    } else {
        $get_district_list = 'select id, district_name from '.$db->table('district').' where city_id = \''.$id.'\'';
        $district_list = $db->fetchAll($get_district_list);
        if( $district_list ) {
            $response['error'] = 0;
            $response['data'] = $district_list;
        } else {
            $response['msg'] = '系统繁忙，请稍后重试';
        }
    }
    echo json_encode($response);
    exit;
}

if( 'add_to_cart' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    $id = intval(getPOST('id'));
    if( 0 >= $id ) {
        $response['msg'] = '参数错误';
    }
    if( $response['msg'] != '' ) {
        echo json_encode($response);
        exit;
    }
    $template = 'add-to-cart.phtml';

    $get_product = 'select p.*,b.name as brand_name from '.$db->table('product').' as p ';
    $get_product .= ' left join '.$db->table('brand').' as b on p.brand_id = b.id';
    $get_product .= ' where  p.`status`=4 and p.`id`='.$id;

    $product = $db->fetchRow($get_product);
    if($product) {
        $response['product_sn'] = $product['product_sn'];
        $product_sn = $product['product_sn'];
        //如果产品正在促销时间，则将产品价格赋值为促销价格
        //促销的产品不参与砍价
        $now = time();
        if ($product['promote_end'] > $now && $product['promote_begin'] <= $now) {
            $product['price'] = $product['promote_price'];
            $product['promote_left'] = $product['promote_end'] - $now;
            $left_time = $product['promote_left'];
            $product['hour'] = intval($left_time / 3600);
            $left_time = $left_time % 3600;
            $product['min'] = intval($left_time / 60);
            $left_time = $left_time % 60;
            $product['second'] = $left_time;
        } else {
            //获取产品砍价总额
            $get_product_discount = 'select sum(`reduce`) from ' . $db->table('discount') .
                ' where `product_sn`=\'' . $product_sn . '\' and `owner`=\'' . $_SESSION['account'] . '\'';

            $discount = $db->fetchOne($get_product_discount);
            $product['price'] -= $discount;
        }


        //读取产品属性表
        if ($product['product_type_id']) {
            $attributes_map = array();
            $attributes_mode = array();
            $get_product_attributes = 'select `id`,`name` from ' . $db->table('product_attributes') . ' where `product_type_id`=' . $product['product_type_id'];

            $attributes = $db->fetchAll($get_product_attributes);

            foreach ($attributes as $a) {
                $attributes_map[$a['id']] = array('id' => $a['id'], 'name' => $a['name']);
                $attributes_mode[$a['id']] = '.*';
            }

            assign('attributes_mode', json_encode($attributes_mode));

            //读取产品库存表
            $get_inventory = 'select `attributes`,`inventory`,`inventory_await`,`inventory_logic` from ' . $db->table('inventory') . ' where `product_sn`=\'' . $product_sn . '\'';
            $inventory = $db->fetchAll($get_inventory);

            $inventory_json = array();
            foreach ($inventory as $inventory_tmp) {
                $attribute_obj = json_decode($inventory_tmp['attributes']);
                foreach ($attribute_obj as $aid => $aval) {
                    if (!isset($attributes_map[$aid]['values'])) {
                        $attributes_map[$aid]['values'] = array();
                    }

                    $attributes_map[$aid]['values'][] = mb_convert_encoding($aval, 'UTF-8');
                }

                $inventory_json[$inventory_tmp['attributes']] = $inventory_tmp['inventory_logic'];
            }

            //如果产品只有一组属性，则默认选中
            if (count($inventory) == 1) {
                assign('attributes_mode', $inventory[0]['attributes']);
                $response['content'] = '';
                $response['attributes'] = json_decode($inventory[0]['attributes']);
            }

            assign('inventory_json', json_encode($inventory_json));
            foreach ($attributes_map as $aid => $value) {
                $attributes_map[$aid]['values'] = array_unique($value['values']);

            }

            if (count($attributes_map) == 1) {
                assign('inventory_logic', $inventory_tmp['inventory_logic']);
            }
            assign('attributes', $attributes_map);
            assign('attributes_json', json_encode($attributes_map));
            assign('product', $product);

            if (count($inventory) == 1) {
                $response['content'] = '';
            } else {
                $response['content'] = $smarty->fetch($template);
            }

        } else {
            $response['content'] = '';
            $response['attributes'] = '';
            //产品没有属性表的情况
            assign('attributes_mode', '""');
            $get_inventory = 'select `attributes`,`inventory`,`inventory_await`,`inventory_logic` from ' . $db->table('inventory') . ' where `product_sn`=\'' . $product_sn . '\'';
            $inventory = $db->fetchAll($get_inventory);

            $inventory_json = array();
            foreach ($inventory as $inventory_tmp) {
                $attribute_obj = '';
                if ($inventory_tmp['attributes'] != '') {
                    $attribute_obj = json_decode($inventory_tmp['attributes']);

                    foreach ($attribute_obj as $aid => $aval) {
                        if (!isset($attributes_map[$aid]['values'])) {
                            $attributes_map[$aid]['values'] = array();
                        }

                        $attributes_map[$aid]['values'][] = mb_convert_encoding($aval, 'UTF-8');
                    }
                } else {
                    $attributes_map = '';
                }

                $inventory_json[$inventory_tmp['attributes']] = $inventory_tmp['inventory_logic'];
                assign('inventory_logic', $inventory_tmp['inventory_logic']);
            }

            assign('inventory_json', json_encode($inventory_json));
            assign('attributes', array());
            assign('attributes_json', '""');
        }
        $response['error'] = 0;
    } else {
        $response['msg'] = '产品不存在';
    }
    echo json_encode($response);
    exit;
}


$id = intval(getGET('id'));

if($id <= 0)
{
    redirect('index.php');
}

$get_product = 'select p.*,b.name as brand_name from '.$db->table('product').' as p ';
$get_product .= ' left join '.$db->table('brand').' as b on p.brand_id = b.id';
$get_product .= ' where  p.`status`=4 and p.`id`='.$id;

$product = $db->fetchRow($get_product);
if($product)
{
    $product_sn = $product['product_sn'];
    //如果产品正在促销时间，则将产品价格赋值为促销价格
    //促销的产品不参与砍价
    $now = time();
    if($product['promote_end'] > $now && $product['promote_begin'] <= $now)
    {
        $product['price'] = $product['promote_price'];
        $product['promote_left'] = $product['promote_end'] - $now;
        $left_time = $product['promote_left'];
        $product['hour'] = intval($left_time/3600);
        $left_time = $left_time%3600;
        $product['min'] = intval($left_time/60);
        $left_time = $left_time%60;
        $product['second'] = $left_time;
    } else if(isset($_SESSION['account'])) {
        //获取产品砍价总额
        $get_product_discount = 'select sum(`reduce`) from '.$db->table('discount').
                                ' where `product_sn`=\''.$product_sn.'\' and `owner`=\''.$_SESSION['account'].'\'';

        $discount = $db->fetchOne($get_product_discount);
        $product['price'] -= $discount;
    }

    $product['gallery'] = array($product['img']);

    //读取产品相册
    $get_gallery = 'select `big_img` from '.$db->table('gallery').' where `product_sn`=\''.$product_sn.'\'';
    $gallery = $db->fetchAll($get_gallery);

    if($gallery)
    {
        $gallery_arr = array();
        foreach($gallery as $g)
        {
            $gallery_arr[] = $g['big_img'];
        }
        $product['gallery'] = array_merge($product['gallery'], $gallery_arr);
    }

    //读取产品属性表
    if($product['product_type_id'])
    {
        $attributes_map = array();
        $attributes_mode = array();
        $get_product_attributes = 'select `id`,`name` from ' . $db->table('product_attributes') . ' where `product_type_id`=' . $product['product_type_id'];

        $attributes = $db->fetchAll($get_product_attributes);

        foreach ($attributes as $a)
        {
            $attributes_map[$a['id']] = array('id' => $a['id'], 'name' => $a['name']);
            $attributes_mode[$a['id']] = '.*';
        }

        assign('attributes_mode', json_encode($attributes_mode));

        //读取产品库存表
        $get_inventory = 'select `attributes`,`inventory`,`inventory_await`,`inventory_logic` from ' . $db->table('inventory') . ' where `product_sn`=\'' . $product_sn . '\'';
        $inventory = $db->fetchAll($get_inventory);

        $inventory_json = array();
        foreach ($inventory as $inventory_tmp)
        {
            $attribute_obj = json_decode($inventory_tmp['attributes']);
            foreach ($attribute_obj as $aid => $aval)
            {
                if (!isset($attributes_map[$aid]['values']))
                {
                    $attributes_map[$aid]['values'] = array();
                }

                $attributes_map[$aid]['values'][] = mb_convert_encoding($aval, 'UTF-8');
            }

            $inventory_json[$inventory_tmp['attributes']] = $inventory_tmp['inventory_logic'];
        }

        //如果产品只有一组属性，则默认选中
        if(count($inventory) == 1)
        {
            assign('attributes_mode', $inventory[0]['attributes']);
        }

        assign('inventory_json', json_encode($inventory_json));
        foreach ($attributes_map as $aid => $value)
        {
            $attributes_map[$aid]['values'] = array_unique($value['values']);

        }

        if(count($attributes_map) == 1)
        {
            assign('inventory_logic', $inventory_tmp['inventory_logic']);
        }
        assign('attributes', $attributes_map);
        assign('attributes_json', json_encode($attributes_map));
    } else {
        //产品没有属性表的情况
        assign('attributes_mode', '""');
        $get_inventory = 'select `attributes`,`inventory`,`inventory_await`,`inventory_logic` from ' . $db->table('inventory') . ' where `product_sn`=\'' . $product_sn . '\'';
        $inventory = $db->fetchAll($get_inventory);

        $inventory_json = array();
        foreach ($inventory as $inventory_tmp)
        {
            $attribute_obj = '';
            if($inventory_tmp['attributes'] != '')
            {
                $attribute_obj = json_decode($inventory_tmp['attributes']);

                foreach ($attribute_obj as $aid => $aval)
                {
                    if (!isset($attributes_map[$aid]['values']))
                    {
                        $attributes_map[$aid]['values'] = array();
                    }

                    $attributes_map[$aid]['values'][] = mb_convert_encoding($aval, 'UTF-8');
                }
            } else {
                $attributes_map = '';
            }

            $inventory_json[$inventory_tmp['attributes']] = $inventory_tmp['inventory_logic'];
            assign('inventory_logic', $inventory_tmp['inventory_logic']);
        }

        assign('inventory_json', json_encode($inventory_json));
        assign('attributes', array());
        assign('attributes_json', '""');
    }

    //读取配送区域
    if( $product['free_delivery'] == 1 ) {
        $product['delivery_fee'] = '免运费';
    } else {
        $get_delivery_area = 'select * from '.$db->table('delivery_area');
        $get_delivery_area .= ' where business_account = \''.$product['business_account'].'\'';
        $delivery_area = $db->fetchAll($get_delivery_area);
        $delivery_fee = 65535;
        if( $delivery_area ) {
            foreach( $delivery_area as $area ) {
                $temp = caculate_delivery_fee($area['first_weight'], $area['next_weight'], $area['free'], $product['weight']);
                $delivery_fee = $delivery_fee > $temp ? $temp : $delivery_fee;
            }
        }
        $product['delivery_fee'] = '￥'.$delivery_fee;
    }

    //配送省
    $get_delivery_province = 'select p.id, p.province_name from '.$db->table('delivery_area_mapper').' as m';
    $get_delivery_province .= ' left join '.$db->table('province').' as p on p.id = m.province';
    $get_delivery_province .= ' where m.business_account = \''.$product['business_account'].'\'';
    $delivery_province = $db->fetchAll($get_delivery_province);
    assign('delivery_province', $delivery_province);

    $product['all_count'] = 0;
    $product['good_count'] = 0;
    $product['normal_count'] = 0;
    $product['bad_count'] = 0;

    //读取评论信息
    $get_comments = 'select c.`id`,c.`comment`,c.`star`,c.`add_time`,m.`headimg`,m.`nickname` from '.$db->table('comment').' as c'.
                    ' join '.$db->table('member').' as m using(`account`) where c.`parent_id`=0 and `product_sn`=\''.$product_sn.'\' order by c.add_time desc';
    $comments = $db->fetchAll($get_comments);
    $good_comments = array();
    $normal_comments = array();
    $bad_comments = array();
    if($comments)
    {
        foreach($comments as $key=>$c)
        {
            $get_reply = 'select `comment`,`add_time` from '.$db->table('comment').' where `parent_id`='.$c['id'];

            $comments[$key]['reply'] = $db->fetchRow($get_reply);

            $product['all_count']++;
            if( $c['star'] == 5 ) {
                $product['good_count'] +=   1;
                $good_comments[] = $comments[$key];
            }
            if($c['star'] > 1 && $c['star'] < 5) {
                $product['normal_count'] += 1;
                $normal_comments[] = $comments[$key];
            }
            if($c['star'] == 1) {
                $product['bad_count'] += 1;
                $bad_comments[] = $comments[$key];
            }
        }
    }
    $product['comments'] = $comments;
    $product['good_comments'] = $good_comments;
    $product['normal_comments'] = $normal_comments;
    $product['bad_comments'] = $bad_comments;


    $all_page = ceil( $product['all_count'] / $page_count );
    $good_page = ceil( $product['good_count'] / $page_count );
    $normal_page = ceil( $product['normal_count'] / $page_count );
    $bad_page = ceil( $product['bad_count'] / $page_count );

    assign('all_page', $all_page);
    assign('good_page', $good_page);
    assign('normal_page', $normal_page);
    assign('bad_page', $bad_page);

    assign('page_count', $page_count);
    assign('comment_count', count($comments));

    //获取商家信息
    $get_business = 'select b.*,p.province_name,city.city_name from '.$db->table('business').' as b';
    $get_business .= ' left join '.$db->table('province').' as p on p.id = b.province';
    $get_business .= ' left join '.$db->table('city').' as city on city.id = b.city';
    $get_business .= ' where b.`business_account`=\''.$product['business_account'].'\' limit 1';
    $product['business'] = $db->fetchRow($get_business);

    if(isset($_SESSION['account']))
    {
        //检查产品的分销状态
        $get_distribution = 'select `product_sn` from ' . $db->table('distribution') .
            ' where `account`=\'' . $_SESSION['account'] . '\' and `product_sn`=\'' . $product_sn . '\'';
        $distribution_flag = $db->fetchOne($get_distribution) ? true : false;
        assign('distribution_flag', $distribution_flag);

        //检查产品的收藏状态
        $get_collection = 'select `product_sn` from ' . $db->table('collection') .
            ' where `account`=\'' . $_SESSION['account'] . '\' and `product_sn`=\'' . $product_sn . '\'';
        $collection_flag = $db->fetchOne($get_collection) ? true : false;
        assign('collection_flag', $collection_flag);
    } else {
        assign('collection_flag', false);
        assign('distribution_flag', false);
    }

    //加入我的足迹
    if(isset($_SESSION['account']) && $_SESSION['account'] != '')
    {
        add_history($_SESSION['account'], $product_sn);
    }

    //猜你喜欢
    $get_fav_products = 'select `name`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,`img`,`id` from '.$db->table('product').' where `status`=4 order by `add_time` DESC limit 4';
    $fav_products = $db->fetchAll($get_fav_products);
    assign('product_list', $fav_products);

    //获取商家分类
    $get_category_path = 'select `path` from '.$db->table('category').' where id = \''.$product['business']['category_id'].'\' limit 1';
    $category_path = $db->fetchOne($get_category_path);

    $get_category_list = 'select `id`, `name`, `path`, `parent_id` from '.$db->table('category').' where business_account = \''.$product['business_account'].'\' order by path asc';
    $category_list = $db->fetchAll($get_category_list);
    $target = array();
    $i = -1;
    if( $category_list ) {
        foreach ($category_list as $key => $category) {
            $category['path'] = substr($category['path'], strlen($category_path) + 1);
            $count = count(explode(',', $category['path']));
            if( $count == 3 ) {
                $target[$i]['children'][] = $category;
            } elseif( $count == 2 ) {
                $target[++$i] = $category;
            }
        }
    }
    assign('shop_category_list', $target);

    if(isset($_SESSION['account']))
    {
        $get_history = 'select p.id,p.name,p.img,if(p.`promote_end`>' . $now . ',p.`promote_price`,p.`price`) as price from ' . $db->table('history') . ' as h';
        $get_history .= ' left join ' . $db->table('product') . ' as p on h.product_sn = p.product_sn';
        $get_history .= ' where h.account = \'' . $_SESSION['account'] . '\'';
        $get_history .= ' order by h.add_time desc limit 5';
        $history = $db->fetchAll($get_history);
        assign('history', $history);
    } else {
        assign('history', null);
    }

    //加入我的足迹
    if(isset($_SESSION['account']) && $_SESSION['account'] != '')
    {
        add_history($_SESSION['account'], $product_sn);
    }

} else {
    redirect('index.php');
}

assign('product', $product);
$smarty->display('product.phtml');