<?php
/**
 * 商户信息管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-08-19
 * @version 1.0.0
 */

include 'library/init.inc.php';

//商户管理后台初始化
business_base_init();
$template = 'profile/';

$action = 'base|auth';
$operation = 'base|auth';

$opera = check_action($operation, getPOST('opera'));
$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'base' : $act;
//======================================================================
if( 'base' == $opera ) {

    $shop_name = trim(getPOST('shop_name'));
    $shop_logo = trim(getPOST('img'));
    $address = trim(getPOST('address'));
    $province = intval(getPOST('province'));
    $city = intval(getPOST('city'));
    $district = intval(getPOST('district'));
    $longitude = floatval(getPOST('lng'));
    $latitude = floatval(getPOST('lat'));

    if( '' == $shop_name ) {
        show_system_message('店名不能为空', array());
        exit;
    }
    $shop_name = $db->escape($shop_name);

    if( '' == $shop_logo ) {
        show_system_message('请选择一张图片作为logo', array());
        exit;
    }
    $shop_logo = $db->escape($shop_logo);


    if( '' == $address ) {
        show_system_message('详细地址不能为空', array());
        exit;
    }
    $address = $db->escape($address);

    if( 0 >= $province ) {
        show_system_message('参数错误', array());
        exit;
    }

    if( 0 >= $city ) {
        show_system_message('参数错误', array());
        exit;
    }

    if( 0 >= $district ) {
        show_system_message('参数错误', array());
        exit;
    }

    if( 0 > $longitude || 360 < $longitude ) {
        show_system_message('参数错误', array());
        exit;
    }

    if( -90 > $latitude || 90 < $latitude ) {
        show_system_message('参数错误', array());
        exit;
    }

    $get_old_shop_logo = 'select `shop_logo` from '.$db->table('business');
    $get_old_shop_logo .= ' where business_account = \''.$_SESSION['business_account'].'\'';
    $get_old_shop_logo .= ' limit 1';

    $old_shop_logo = $db->fetchOne($get_old_shop_logo);

    $data = array(
        'shop_name' => $shop_name,
        'shop_logo' => $shop_logo,
        'address' => $address,
        'province' => $province,
        'city' => $city,
        'district' => $district,
        'longitude' => $longitude,
        'latitude' => $latitude,
    );
    $table = 'business';
    $where = 'business_account = \''.$_SESSION['business_account'].'\'';
    $order = '';
    $limit = '';

    if( $db->autoUpdate($table, $data, $where, $order, $limit) ) {

//        if( !empty($old_shop_logo) && ($old_shop_logo != $shop_logo) && file_exists(realpath('..'.$old_shop_logo)) ) {
//            unlink(realpath('..'.$old_shop_logo));
//        }
        show_system_message('更新商户信息成功', array());
        exit;
    } else {
        show_system_message('系统繁忙，请稍后重试', array());
        exit;
    }


}

if( 'auth' == $opera ) {

}
//======================================================================

if( 'base' == $act ) {

    $get_business = 'select `business_account`, `shop_logo`, `shop_name`, `address`, `province`, `city`, `district`, `group`, `longitude`, `latitude`';
    $get_business .= ' from '.$db->table('business');
    $get_business .= ' where `business_account` = \''.$_SESSION['business_account'].'\'';
    $get_business .= ' limit 1';
    $business = $db->fetchRow($get_business);
    assign('business', $business);

    $get_province_list = 'select * from '.$db->table('province').' where 1 order by id asc';
    $province_list = $db->fetchAll($get_province_list);

    $get_city_list = 'select * from '.$db->table('city').' where 1 order by id asc';
    $city_list = $db->fetchAll($get_city_list);

    $get_district_list = 'select * from '.$db->table('district').' where 1 order by id asc';
    $district_list = $db->fetchAll($get_district_list);

    $get_group_list = 'select * from '.$db->table('group').' where 1 order by id asc';
    $group_list = $db->fetchAll($get_group_list);

    //转换城市的结构
    $target_city_list = array();
    $count = count($city_list);
    for($i = 0; $i < $count; ) {
        $pid = $city_list[$i]['province_id'];
        $temp = array();
        do {
            $temp[] = $city_list[$i];
            $i++;
        } while($i < $count && $city_list[$i]['province_id'] == $pid);
        $target_city_list[$pid] = $temp;
    }
    //转换地区的结构
    $target_district_list = array();
    $count = count($district_list);
    for($i = 0; $i < $count; ) {
        $pid = $district_list[$i]['city_id'];
        $temp = array();
        do {
            $temp[] = $district_list[$i];
            $i++;
        } while($i < $count && $district_list[$i]['city_id'] == $pid);
        $target_district_list[$pid] = $temp;
    }

    assign('province_list', $province_list);
    assign('city_list', $city_list);
    assign('district_list', $district_list);
    assign('json_cities', json_encode($target_city_list));
    assign('json_districts', json_encode($target_district_list));
    assign('map_init', json_encode(true));
}

if( 'auth' == $act ) {

}
$template .= $act.'.phtml';
$smarty->display($template);
