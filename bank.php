<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/17
 * Time: 上午10:24
 */
include 'library/init.inc.php';
$template = 'bank.phtml';

$action = 'add|edit|list|delete';
$operation = 'add|edit';

$opera = check_action($operation, getPOST('opera'));
$act = check_action($action, getGET('act'));

if('' == $act)
{
    $act = 'list';
}

if('add' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');
    $bank = getPOST('bank');
    $bank_account = getPOST('bank_account');
    $bank_card = getPOST('bank_card');
    $password = getPOST('password');

    if($bank == '')
    {
        $response['msg'] .= '-请填写开户银行<br/>';
    } else {
        $bank = $db->escape($bank);
    }

    if($bank_account == '')
    {
        $response['msg'] .= '-请填写开户人姓名<br/>';
    } else {
        $bank_account = $db->escape($bank_account);
    }

    if($bank_card == '')
    {
        $response['msg'] .= '-请填写银行卡号<br/>';
    } else {
        $bank_card = $db->escape($bank_card);
    }

    if($password == '')
    {
        $response['msg'] .= '-请填写账户密码<br/>';
    } else {
        if (!verify_password($_SESSION['account'], $password))
        {
            $response['msg'] .= '-账户密码错误<br/>';
        }
    }

    if($response['msg'] == '')
    {
        $bank_card_data = array(
            'account' => $_SESSION['account'],
            'bank' => $bank,
            'bank_card' => $bank_card,
            'bank_account' => $bank_account
        );

        if($db->autoInsert('bank_card', array($bank_card_data)))
        {
            $response['error'] = 0;
            $response['msg'] = '添加银行卡成功';
        } else {
            $response['msg'] = '001:系统繁忙，请稍后再试';
        }
    }

    echo json_encode($response);
    exit;
}

if('add' == $act)
{
    $template = 'add_bank.phtml';
}

if('list' == $act)
{
    $template = 'bank_list.phtml';
}

$smarty->display($template);