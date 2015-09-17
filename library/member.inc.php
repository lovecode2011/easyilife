<?php
/**
 * 会员操作封装
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:12
 */

 /**
 * 账户明细
 * @param string $account
 * @param float $balance
 * @param float $reward
 * @param float $integral
 * @param float $integral_await
 * @param float $reward_await
 * @param string $operator
 * @param string $remark
 * @return bool
 */
 function add_memeber_exchange_log($account, $balance, $reward, $integral, $integral_await, $reward_await, $operator, $remark = '')
 {
    global $db;

    $exchange_data = array(
        'account' => $account,
        'balance' => $balance,
        'reward' => $reward,
        'integral' => $integral,
        'integral_await' => $integral_await,
        'reward_await' => $reward_await,
        'operator' => $operator,
        'remark' => $remark,
        'add_time' => time()
    );

    $db->begin();

    //更新用户账户
    $update_user = 'update '.$db->table('member').' set `balance`=`balance`+'.$balance.',`reward`=`reward`+'.$reward.','.
                   '`integral`=`integral`+'.$integral.',`integral_await`=`integral_await`+'.$integral_await.','.
                   '`reward_await`=`reward_await`+'.$reward_await.' where `account`=\''.$account.'\'';
    if($db->update($update_user))
    {
        if($db->autoInsert('member_exchange_log', array($exchange_data)))
        {
            $db->commit();
            return true;
        }
    }

    $db->rollback();
    return false;
 }

/**
 * 修改收货地址
 * @param int $province
 * @param int $city
 * @param int $district
 * @param int $group
 * @param string $address
 * @param string $consignee
 * @param string $mobile
 * @param string $zipcode
 * @param string $account
 * @param bool $is_default
 * @param int $id
 * @return bool
 */
 function edit_address($province, $city, $district, $group, $address, $consignee, $mobile, $zipcode, $account, $is_default, $id)
 {
    global $db;

    $address_data = array(
        'province' => $province,
        'city' => $city,
        'district' => $district,
        'group' => $group,
        'address' => $address,
        'consignee' => $consignee,
        'mobile' => $mobile,
        'zipcode' => $zipcode,
        'is_default' => $is_default
    );

    return $db->autoUpdate('address', $address_data, '`id`='.$id.' and `account`=\''.$account.'\'');
 }

/**
 * 新增收货地址
 * @param int $province
 * @param int $city
 * @param int $district
 * @param int $group
 * @param string $address
 * @param string $consignee
 * @param string $mobile
 * @param string $zipcode
 * @param string $account
 * @param bool $is_default
 * @return int
 */
 function add_address($province, $city, $district, $group, $address, $consignee, $mobile, $zipcode, $account, $is_default = true)
 {
    global $db;

    $address_data = array(
        'province' => $province,
        'city' => $city,
        'district' => $district,
        'group' => $group,
        'address' => $address,
        'consignee' => $consignee,
        'mobile' => $mobile,
        'zipcode' => $zipcode,
        'account' => $account,
        'is_default' => $is_default
    );

    if($db->autoInsert('address', array($address_data)))
    {
        return $db->get_last_id();
    } else {
        return false;
    }
 }
/**
 * 取消分销产品
 * @param string $account
 * @param string $product_sn
 * @return bool
 */
 function cancel_distribution_product($account, $product_sn)
 {
    global $db;

    return $db->autoDelete('distribution', '`account`=\''.$account.'\' and `product_sn`=\''.$product_sn.'\'');
 }

 /**
 * 分销产品
 * @param string $account
 * @param string $product_sn
 * @return bool
 */
 function distribution_product($account, $product_sn)
 {
    global $db;
    global $log;

    //检查产品是否已分销
    $check_distribution = 'select `account` from '.$db->table('distribution').' where `account`=\''.$account.'\' and `product_sn`=\''.$product_sn.'\'';
    if($db->fetchOne($check_distribution))
    {
        return true;
    } else {
        $distribution_data = array(
            'account' => $account,
            'product_sn' => $product_sn,
            'add_time' => time()
        );

        $log->record_array($distribution_data);
        return $db->autoInsert('distribution', array($distribution_data));
    }
 }

 /**
 * 取消收藏产品
 * @param string $account
 * @param string $product_sn
 * @return bool
 */
 function cancel_collection_product($account, $product_sn)
 {
    global $db;

    return $db->autoDelete('collection', '`account`=\''.$account.'\' and `product_sn`=\''.$product_sn.'\'');
 }

 /**
 * 收藏产品
 * @param string $account
 * @param string $product_sn
 * @return bool
 */
 function collection_product($account, $product_sn)
 {
    global $db;
    global $log;

    //检查产品是否已收藏
    $check_collection = 'select `account` from '.$db->table('collection').' where `account`=\''.$account.'\' and `product_sn`=\''.$product_sn.'\'';
    if($db->fetchOne($check_collection))
    {
        return true;
    } else {
        $collection_data = array(
            'account' => $account,
            'product_sn' => $product_sn,
            'add_time' => time()
        );

        $log->record_array($collection_data);

        return $db->autoInsert('collection', array($collection_data));
    }
 }

 /**
 * 同步微信信息
 * @param string $openid
 * @param string $code
 * @return bool
 */
 function async_member_info($openid, $code)
 {
    global $db;
    global $config;
    global $log;

    $user_info = get_user_info($code, $config['appid'], $config['appsecret'], 'userinfo');

    if($user_info)
    {
        $member_data = array(
            'sex' => $user_info->sex,
            'nickname' => $user_info->nickname,
            'province' => $user_info->province,
            'city' => $user_info->city,
            'headimg' => $user_info->headimg,
            'unionid' => $user_info->unionid
        );

        $log->record_array($member_data);
        if($db->autoUpdate('member', $member_data, '`openid`=\''.$openid.'\''))
        {
            return true;
        } else {
            return false;
        }
    }
 }

 /**
 * 注册会员信息
 * @param string $openid
 * @return string 会员账号
 */
 function register_member($openid, $parent_id = 0)
 {
    global $db;
    global $log;

    $db->begin();
    $member_data = array(
        'account' => get_account(),
        'openid' => $openid,
        'add_time' => time(),
        'parent_id' => $parent_id
    );

    if($db->autoInsert('member', array($member_data)))
    {
        $path = '';
        $id = $db->get_last_id();
        if($parent_id)
        {
            $get_parent_path = 'select `path` from '.$db->table('member').' where `id`='.$parent_id;
            $path = $db->fetchOne($get_parent_path);
        }

        $path .= $id.',';

        $update_data = array(
            'path' => $path
        );

        if($db->autoUpdate('member', $update_data, '`id`='.$id))
        {
            $log->record_array($member_data);
            $db->commit();
            return $member_data['account'];
        }
    }

    $db->rollback();
    return false;
 }

 /**
 * 获取会员账号
 * @param int $step 账号池增长的步长，实际账号池增长速度为2*$step
 * @return string 会员账号
 */
 function get_account($step = 500)
 {
    global $db;
    global $log;

    $db->begin();

    $get_account = 'select `account` from '.$db->table('card_pool').' where `status`=1 order by `id` ASC';
    $log->record('get account:'.$get_account);
    $account = $db->fetchOne($get_account);

    //检查剩余的可用账号如果少于step则扩充账号池
    $get_account_count = 'select count(*) from '.$db->table('card_pool').' where `status`=1 for update';
    $account_left = $db->fetchOne($get_account_count);
    if($account_left < $step)
    {
        $get_max_account = 'select `account` from '.$db->table('card_pool').' order by `account` DESC limit 1';
        $max_account = $db->fetchOne($get_max_account);

        $account_prefix = substr($max_account, 0, 2);
        $max = str_replace($account_prefix, '', $max_account);
        $max = intval($max);

        $account_arr = range($max, $max + $step);
        shuffle($account_arr);

        $data = array();
        foreach($account_arr as $account_tail)
        {
            $c = strlen($account_tail);
            while($c < 6)
            {
                $account_tail = '0'.$account_tail;
                $c++;
            }

            $data[] = array('account'=>$account_prefix.$account_tail);
        }
        $log->record_array($data);
        $db->autoInsert('card_pool', $data);
    }
    $status = array('status'=>0);
    $db->autoUpdate('card_pool', $status, '`account`=\''.$account.'\'');
    $db->commit();

    return $account;
 }

/**
 * 发放推广积分
 * @param $account
 * @param $integral
 * @param $remark
 * @return bool
 */
function add_recommend_integral($account, $integral, $remark = '', $operator = 'settle')
{
    return add_memeber_exchange_log($account, 0, 0, $integral, 0,0, $operator, $remark);
}