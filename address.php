<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/11
 * Time: 上午11:28
 */
include 'library/init.inc.php';

$operation = 'add|edit|delete|get_info';
$action = 'add|edit|select|list';

$act = check_action($action, getGET('act', 'list'));
$opera = check_action($operation, getPOST('opera'));

if($act == '')
{
    $act = 'list';
}

$template = 'address.phtml';

if('add' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $province = intval(getPOST('province'));
    $city = intval(getPOST('city'));
    $district = intval(getPOST('district'));
    $group = intval(getPOST('group'));
    $address = getPOST('address');
    $is_default = getPOST('is_default');
    $mobile = getPOST('mobile');
    $consignee = getPOST('consignee');

    if(!check_cross_domain())
    {
        if($province <= 0)
        {
            $response['msg'] .= "-请选择省份\n";
        }

        if($city <= 0)
        {
            $response['msg'] .= "-请选择城市\n";
        }

        if($district <= 0)
        {
            $response['msg'] .= "-请选择地区\n";
        }

        if($group <= 0)
        {
            $response['msg'] .= "-请选择商圈\n";
        }

        if($address == '')
        {
            $response['msg'] .= "-请填写街道地址\n";
        } else {
            $address = $db->escape($address);
        }

        if($mobile == '')
        {
            $response['msg'] .= "-请填写手机号码";
        } else {
            $mobile = $db->escape($mobile);
        }

        if($is_default == 'true')
        {
            $is_default = true;
        } else {
            $is_default = false;
        }

        if($response['msg'] == '')
        {
            if($is_default)
            {
                $db->autoUpdate('address', array('is_default'=>0), '`account`=\''.$_SESSION['account'].'\'');
            }

            $address_data = array(
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'group' => $group,
                'address' => $address,
                'mobile' => $mobile,
                'consignee' => $consignee,
                'is_default' => $is_default,
                'account' => $_SESSION['account']
            );

            if($db->autoInsert('address', array($address_data)))
            {
                $response['error'] = 0;
                $response['msg'] = '新增收货地址成功';
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

if('edit' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $province = intval(getPOST('province'));
    $city = intval(getPOST('city'));
    $district = intval(getPOST('district'));
    $group = intval(getPOST('group'));
    $address = getPOST('address');
    $is_default = getPOST('is_default');
    $mobile = getPOST('mobile');
    $consignee = getPOST('consignee');
    $id = intval(getPOST('address_id'));

    if(!check_cross_domain())
    {
        if($id <= 0)
        {
            $response['msg'] .= "403:参数错误\n";
        }

        if($province <= 0)
        {
            $response['msg'] .= "-请选择省份\n";
        }

        if($city <= 0)
        {
            $response['msg'] .= "-请选择城市\n";
        }

        if($district <= 0)
        {
            $response['msg'] .= "-请选择地区\n";
        }

        if($group <= 0)
        {
            $response['msg'] .= "-请选择商圈\n";
        }

        if($address == '')
        {
            $response['msg'] .= "-请填写街道地址\n";
        } else {
            $address = $db->escape($address);
        }

        if($mobile == '')
        {
            $response['msg'] .= "-请填写手机号码";
        } else {
            $mobile = $db->escape($mobile);
        }

        if($is_default == 'true')
        {
            $is_default = true;
        } else {
            $is_default = false;
        }

        if($response['msg'] == '')
        {
            if($is_default)
            {
                $db->autoUpdate('address', array('is_default'=>0), '`account`=\''.$_SESSION['account'].'\'');
            }

            $address_data = array(
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'group' => $group,
                'address' => $address,
                'mobile' => $mobile,
                'consignee' => $consignee,
                'is_default' => $is_default
            );

            if($db->autoUpdate('address', $address_data, '`id`='.$id.' and `account`=\''.$_SESSION['account'].'\''))
            {
                $response['error'] = 0;
                $response['msg'] = '收货地址修改成功';
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

if('get_info' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $id = intval(getPOST('address_id'));

    if(!check_cross_domain())
    {
        $get_address_detail = 'select p.`province_name`,c.`city_name`,d.`district_name`,g.`group_name`,a.`address`,a.`consignee`,'.
                              'a.`mobile`,a.`zipcode`,a.`id` from '.$db->table('address').' as a, '.$db->table('province').' as p, '.
                              $db->table('city').' as c, '.$db->table('district').' as d, '.$db->table('group').' as g where '.
                              'a.`province`=p.`id` and a.`city`=c.`id` and a.`district`=d.`id` and a.`group`=g.`id` and a.`id`='.$id.
                              ' and a.`account`=\''.$_SESSION['account'].'\'';

        $address_info = $db->fetchRow($get_address_detail);

        if($address_info)
        {
            $response['error'] = 0;
            $response['address'] = $address_info['province_name'].' '.$address_info['city_name'].' '.$address_info['district_name'].' '.
                                   $address_info['group_name'].' '.$address_info['address'];
            $response['consignee'] = $address_info['consignee'];
            $response['mobile'] = $address_info['mobile'];
            $response['zipcode'] = $address_info['zipcode'];
            $response['id'] = $address_info['id'];
        } else {
            $response['msg'] = '000:参数错误';
        }
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}

if('select' == $act)
{
    $template = 'select_address.phtml';

    $get_address_list = 'select a.`is_default`,p.`province_name`,c.`city_name`,d.`district_name`,g.`group_name`,a.`address`,a.`consignee`,'.
                        'a.`mobile`,a.`zipcode`,a.`id` from '.$db->table('address').' as a, '.$db->table('province').' as p, '.
                        $db->table('city').' as c, '.$db->table('district').' as d, '.$db->table('group').' as g where '.
                        'a.`province`=p.`id` and a.`city`=c.`id` and a.`district`=d.`id` and a.`group`=g.`id` '.
                        ' and a.`account`=\''.$_SESSION['account'].'\'';

    $address_list = $db->fetchAll($get_address_list);

    foreach($address_list as $key=>$address)
    {
        $address_list[$key]['detail_address'] = $address['province_name'].' '.$address['city_name'].' '.$address['district_name'].' '.
            $address['group_name'].' '.$address['address'];
    }

    assign('address_list', $address_list);
}

if('edit' == $act)
{
    $template = 'edit_address.phtml';

    $id = intval(getGET('id'));
    if($id <= 0)
    {
        redirect($_SERVER['HTTP_REFERER']);
    }

    $get_address = 'select * from '.$db->table('address').' where `id`='.$id.' and `account`=\''.$_SESSION['account'].'\'';
    $address = $db->fetchRow($get_address);

    if($address)
    {
        assign('address', $address);
    } else {
        redirect($_SERVER['HTTP_REFERER']);
    }

    $get_province = 'select `id`,`province_name` as `name` from '.$db->table('province');
    $province = $db->fetchAll($get_province);
    assign('province', $province);

    $get_city = 'select `id`,`city_name` as `name`,`province_id` from '.$db->table('city');
    $city = $db->fetchAll($get_city);
    assign('city', $city);
    $city_json = array();
    foreach($city as $c)
    {
        if(!isset($city_json[$c['province_id']]))
        {
            $city_json[$c['province_id']] = array();
        }

        $city_json[$c['province_id']][] = $c;
    }
    assign('city_json', json_encode($city_json));

    $get_district = 'select `id`,`district_name` as `name`,`city_id` from '.$db->table('district');
    $district = $db->fetchAll($get_district);
    assign('district', $district);
    $district_json = array();
    foreach($district as $d)
    {
        if(!isset($district_json[$d['city_id']]))
        {
            $district_json[$d['city_id']] = array();
        }

        $district_json[$d['city_id']][] = $d;
    }
    assign('district_json', json_encode($district_json));

    $get_group = 'select `id`,`group_name` as `name`,`district_id` from '.$db->table('group');
    $group = $db->fetchAll($get_group);
    assign('group', $group);
    $group_json = array();
    foreach($group as $g)
    {
        if(!isset($group_json[$g['district_id']]))
        {
            $group_json[$g['district_id']] = array();
        }

        $group_json[$g['district_id']][] = $g;
    }
    assign('group_json', json_encode($group_json));
}

if('add' == $act)
{
    $template = 'add_address.phtml';

    $get_province = 'select `id`,`province_name` as `name` from '.$db->table('province');
    $province = $db->fetchAll($get_province);
    assign('province', $province);

    $get_city = 'select `id`,`city_name` as `name`,`province_id` from '.$db->table('city');
    $city = $db->fetchAll($get_city);
    assign('city', $city);
    $city_json = array();
    foreach($city as $c)
    {
        if(!isset($city_json[$c['province_id']]))
        {
            $city_json[$c['province_id']] = array();
        }

        $city_json[$c['province_id']][] = $c;
    }
    assign('city_json', json_encode($city_json));

    $get_district = 'select `id`,`district_name` as `name`,`city_id` from '.$db->table('district');
    $district = $db->fetchAll($get_district);
    assign('district', $district);
    $district_json = array();
    foreach($district as $d)
    {
        if(!isset($district_json[$d['city_id']]))
        {
            $district_json[$d['city_id']] = array();
        }

        $district_json[$d['city_id']][] = $d;
    }
    assign('district_json', json_encode($district_json));

    $get_group = 'select `id`,`group_name` as `name`,`district_id` from '.$db->table('group');
    $group = $db->fetchAll($get_group);
    assign('group', $group);
    $group_json = array();
    foreach($group as $g)
    {
        if(!isset($group_json[$g['district_id']]))
        {
            $group_json[$g['district_id']] = array();
        }

        $group_json[$g['district_id']][] = $g;
    }
    assign('group_json', json_encode($group_json));
}

if('list' == $act)
{
    $get_address_list = 'select a.`is_default`,p.`province_name`,c.`city_name`,d.`district_name`,g.`group_name`,a.`address`,a.`consignee`,'.
        'a.`mobile`,a.`zipcode`,a.`id` from '.$db->table('address').' as a, '.$db->table('province').' as p, '.
        $db->table('city').' as c, '.$db->table('district').' as d, '.$db->table('group').' as g where '.
        'a.`province`=p.`id` and a.`city`=c.`id` and a.`district`=d.`id` and a.`group`=g.`id` '.
        ' and a.`account`=\''.$_SESSION['account'].'\' order by `is_default` DESC';

    $address_list = $db->fetchAll($get_address_list);

    foreach($address_list as $key=>$address)
    {
        $address_list[$key]['detail_address'] = $address['province_name'].' '.$address['city_name'].' '.$address['district_name'].' '.
            $address['group_name'].' '.$address['address'];
    }

    assign('address_list', $address_list);
}

assign('act', $act);
$smarty->display($template);