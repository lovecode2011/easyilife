<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 下午4:46
 */
include 'library/init.inc.php';

$operation = 'add|delete';
$action = 'list|opera|detail';
$act = check_action($action, getGET('act'));
$opera = check_action($operation, getPOST('opera'));

$template = 'withdraw.phtml';

$get_user_info = 'select `balance`,`reward`,`password` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';

$user_info = $db->fetchRow($get_user_info);

//获取未提现的金额
$get_withdraw_await = 'select sum(`amount`+`fee`) from '.$db->table('withdraw').' where `account`=\''.$_SESSION['account'].'\'';
$withdraw_await = $db->fetchOne($get_withdraw_await);

$withdraw_await = $user_info['balance'] + $user_info['reward'] - $withdraw_await;

if('add' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    if(!check_cross_domain())
    {
        $bank_id = intval(getPOST('bank_id'));
        $amount = floatval(getPOST('amount'));
        $password = getPOST('password');

        if($bank_id <= 0)
        {
            $response['msg'] .= '-请选择银行卡<br/>';
        } else {
            $check_bank_info = 'select `account` from '.$db->table('bank_card').' where `id`='.$bank_id;

            $card_account = $db->fetchOne($check_bank_info);

            if($card_account != $_SESSION['account'])
            {
                $response['msg'] .= '-参数错误<br/>';
            }
        }

        if($amount <= 0)
        {
            $response['msg'] .= '-请填写提现金额<br/>';
        } else {
            $total_amount = (1+$config['fee_rate']) * $amount;

            if($total_amount > $withdraw_await)
            {
                $response['msg'] .= '-可提现金额不足<br/>';
            }
        }

        if($password == '')
        {
            $response['msg'] .= '-请填写账户密码<br/>';
        } else {
            if(!verify_password($_SESSION['account'], $password))
            {
                $response['msg'] .= '-账户密码错误<br/>';
            }
        }

        if($response['msg'] == '')
        {
            if($withdraw_sn = add_withdraw($_SESSION['account'], $amount, $bank_id))
            {
                $response['error'] = 0;
                $response['msg'] = '提现申请提交成功';
                $response['withdraw_sn'] = $withdraw_sn;
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

if($act == '')
{
    $act = 'opera';
}

if('opera' == $act)
{
    //检查密码是否已设置
    if($user_info['password'] == '')
    {
        redirect('bind_mobile.php');
    }
    assign('withdraw_await', $withdraw_await);

    //获取用户银行卡
    $get_bank_list = 'select `id`,`bank`,`bank_account`,`bank_card` from '.$db->table('bank_card').' where `account`=\''.$_SESSION['account'].'\'';
    $bank_list = $db->fetchAll($get_bank_list);
    if($bank_list)
    {
        foreach ($bank_list as $key => $bank)
        {
            $bank_list[$key]['show_name'] = $bank['bank'] . '(尾号' . substr($bank['bank_card'], -4) . ')';
        }
    }

    assign('bank_list', $bank_list);
}

if('list' == $act)
{

}

$smarty->display($template);