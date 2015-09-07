<?php
/**
 * 会员操作封装
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:12
 */

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