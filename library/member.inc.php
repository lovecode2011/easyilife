<?php
/**
 * 会员操作封装
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:12
 */

/**
* 修改充值操作记录
*/
function update_recharge($recharge_sn, $status, $operator, $remark)
{

}

/**
* 新增充值申请
*/
function add_recharge_application($account, $recharge, $remark, $bank_name, $bank_account, $bank_card, $status = 0, $operator = '')
{
    global $db;

    $db->begin();

    $data = array(
        'account' => $account,
        'amount' => $recharge,
        'real_amount' => $recharge,
        'remark' => $remark,
        'bank_name' => $bank_name,
        'bank_card' => $bank_card,
        'bank_account' => $bank_account,
        'status' => $status
    );

    $recharge_sn = '';
    do
    {
        $recharge_sn = 'R'.time().rand(100, 999);
        $check_recharge_sn = 'select `recharge_sn` from '.$db->table('recharge').' where `recharge_sn`=\''.$recharge_sn.'\'';
    } while($db->fetchOne($check_recharge_sn));

    if($recharge_sn)
    {
        if($db->autoInsert('recharge', array($data)))
        {
            $log_data = array(
                'recharge_sn' => $recharge_sn,
                'add_time' => time(),
                'operator' => $operator
            );

            if($operator == '')
            {
                $log_data['operator'] = $account;
                $log_data['remark'] = '提交充值申请';
            }

            if($db->autoInsert('recharge_log', array($log_data)))
            {
                $db->commit();
                return $recharge_sn;
            }
        }
    }

    $db->rollback();
    return false;
}
/**
* 修改提现申请
*/
function update_withdraw($apply_sn, $status, $operator, $remark)
{
    global $db;

    $db->begin();

    $data = array(
        'status' => $status
    );

    if($db->autoUpdate('withdraw', $data, '`apply_sn`=\''.$apply_sn.'\''))
    {
        $log_data = array(
            'apply_sn' => $apply_sn,
            'add_time' => time(),
            'operator' => $operator,
            'remark' => $remark
        );

        if($db->autoInsert('withdraw_log', array($log_data)))
        {
            $db->commit();
            return true;
        }
    }

    $db->rollback();
    return false;
}
/**
* 新增提现申请
*/
function add_withdraw_application($account, $withdraw, $fee, $remark, $bank_name, $bank_account, $bank_card)
{
    global $db;
    global $log;

    $db->begin();

    $data = array(
        'account' => $account,
        'amount' => $withdraw,
        'fee' => $fee,
        'apply_time' => time(),
        'remark' => $remark,
        'bank_name' => $bank_name,
        'bank_account' => $bank_account,
        'bank_card' => $bank_card
    );

    $apply_sn = '';
    do
    {
        $apply_sn = 'A'.time().rand(100, 999);
        $check_apply_sn = 'select `apply_sn` from '.$db->table('withdraw').' where `apply_sn`=\''.$apply_sn.'\'';
    } while($db->fetchOne($check_apply_sn));

    if($apply_sn)
    {
        $data['apply_sn'] = $apply_sn;

        if($db->autoInsert('withdraw', array($data)))
        {
            $log_data = array(
                'apply_sn' => $apply_sn,
                'add_time' => time(),
                'operator' => $account,
                'remark' => '提交申请'
            );

            if($db->autoInsert('withdraw_log', array($log_data)))
            {
                $db->commit();
                return $apply_sn;
            }
        }
    }

    $db->rollback();
    return false;
}

/**
* 新增赠送积分
*/
function add_integral_given($account, $integral_given, $remark, $operator = 'settle')
{
    global $db;
    global $log;

    $db->begin();

    if(add_member_account($account, $operator, 3, 0, $integral_given, 0, 0, 0, $remark))
    {
        $data = array(
            'account' => $account,
            'integral_await' => $integral_given,
            'add_time' => time()
        );

        if($db->autoInsert('reward', array($data)))
        {
            $db->commit();
            return true;
        }
    }

    $db->rollback();
    return false;
}
/**
* 新增推广积分
*/
function add_recommend_integral($account, $integral, $remark, $operator = 'settle')
{
    global $db;
    global $log;

    $db->begin();

    if(add_member_account($account, $operator, 3, $integral, 0, 0, 0, 0, $remark))
    {
        $db->commit();
        return true;
    }

    $db->rollback();
    return false;
}

/**
* 新增分销奖金
*/
function add_reward($account, $reward, $remark, $operator = 'settle')
{
    global $db;
    global $log;

    $db->begin();

    if(add_member_account($account, $operator, 3, 0, 0, 0, $reward, 0, $remark))
    {
        $data = array(
            'account' => $account,
            'reward_await' => $reward,
            'add_time' => time()
        );

        if($db->autoInsert('reward', array($data)))
        {
            $db->commit();
            return true;
        }
    }

    $db->rollback();
    return false;
}
/**
* 会员积分互转
*/
function integral_transfer($from, $to, $integral, $remark)
{
    global $db;
    global $log;

    $db->begin();

    $check_integral = 'select `integral` from '.$db->table('user').' where `account`=\''.$from.'\' for update';
    $from_integral = $db->fetchOne($check_integral);

    if($from_integral >= $integral)
    {
        if(add_member_account($from, $from, 1, -1*$integral, 0, 0, 0, 0, $remark))
        {
            if(add_member_account($to, $from, 2, $integral, 0, 0, 0, 0, $remark))
            {
                $db->commit();
                return true;
            }
        }
    }

    $db->rollback();
    return false;
}

/**
* 记录会员账户明细
*/
function add_member_account($account, $operator, $type, $integral = 0, $integral_await = 0, $reward = 0, $reward_await = 0, $emoney = 0, $remark = '')
{
    global $db;
    global $log;

    $update_user = 'update '.$db->table('user').' set `integral`=`integral`+%f,`integral_await`=`integral_await`+%f,'.
                   '`reward`=`reward`+%f,`reward_await`=`reward_await`+%f,`emoney`=`emoney`+%f where `account`=\'%s\'';
    $update_user = sprintf($update_user, $integral, $integral_await, $reward, $reward_await, $emoney, $account);
    $log->record($update_user);
    if($db->update($update_user))
    {
        $data = array(
            'account' => $account,
            'operator' => $operator,
            'integral' => $integral,
            'integral_await' => $integral_await,
            'reward' => $reward,
            'reward_await' => $reward_await,
            'emoney' => $emoney,
            'remark' => $remark,
            'add_time' => time(),
            'type' => $type
        );

        $db->autoInsert('account', array($data));

        return true;
    } else {
        return false;
    }
}

/**
 * 新增会员账号
 */
function add_user($openid, $parent_id = 0)
{
    global $db;

    //scene_id可能已满但不影响获取二维码操作
    $data = array(
        'openid' => $openid,
        'scene_id' => 0,
        'account' => get_account(),
        'parent_id' => $parent_id
    );

    if($db->autoInsert('user', array($data)))
    {
        if($parent_id > 0)
        {
            $self_id = $db->get_last_id();
            $get_parent_path = 'select `path` from '.$db->table('user').' where `id`='.$parent_id;
            $parent_path = $db->fetchOne($get_parent_path);

            $path = $parent_path.$self_id.',';

            $db->autoUpdate('user', array('path'=>$path), '`openid`=\''.$openid.'\'');
        }
    }

    return $data['account'];
}

 /**
 * 获取会员账号
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