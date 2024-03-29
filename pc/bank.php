<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-21
 * @version 
 */

include "library/init.inc.php";

$action = 'list|add|edit';
$operation = 'add|edit|delete';

$act = check_action($action, getGET('act'));
$opera = check_action($operation, getPOST('opera'));

$act = ($act == '') ? 'list' : $act;

if('delete' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $id = getPOST('eid');

    $id = intval($id);
    if($id <= 0)
    {
        $response['msg'] = '-参数错误<br/>';
    }

    if($response['msg'] == '')
    {
        if($db->autoDelete('bank_card', '`id`='.$id.' and `account`=\''.$_SESSION['account'].'\''))
        {
            $response['error'] = 0;
            $response['msg'] = '删除银行卡成功';
        } else {
            $response['msg'] = '001:系统繁忙，请稍后再试';
        }
    }

    echo json_encode($response);
    exit;
}

if('edit' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');
    $bank = getPOST('bank');
    $bank_account = getPOST('bank_account');
    $bank_card = getPOST('bank_card');
    $password = getPOST('password');
    $mobile = getPOST('mobile');
    $id = getPOST('eid');

    $id = intval($id);
    if($id <= 0)
    {
        $response['msg'] = '-参数错误<br/>';
    }

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

    if($mobile == '')
    {
        $response['msg'] .= '-请填写手机号码<br/>';
    } else {
        $mobile = $db->escape($mobile);
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
            'bank_account' => $bank_account,
            'mobile' => $mobile
        );

        if($db->autoUpdate('bank_card', $bank_card_data, '`id`='.$id.' and `account`=\''.$_SESSION['account'].'\''))
        {
            $response['error'] = 0;
            $response['msg'] = '修改银行卡成功';
        } else {
            $response['msg'] = '001:系统繁忙，请稍后再试';
        }
    }

    echo json_encode($response);
    exit;
}

if('add' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');
    $bank = getPOST('bank');
    $bank_account = getPOST('bank_account');
    $bank_card = getPOST('bank_card');
    $password = getPOST('password');
    $mobile = getPOST('mobile');

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

    if($mobile == '')
    {
        $response['msg'] .= '-请填写手机号码<br/>';
    } else {
        $mobile = $db->escape($mobile);
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
            'bank_account' => $bank_account,
            'mobile' => $mobile
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

if( 'add' == $act ) {

}

if( 'edit' == $act ) {
    $id = intval(getGET('id'));

    if($id <= 0)
    {
        redirect('bank.php');
    }

    $get_bank_card = 'select `id`,`bank`,`bank_account`,`bank_card`,`mobile` from '.$db->table('bank_card').
        ' where `id`='.$id.' and `account`=\''.$_SESSION['account'].'\'';

    $bank_card = $db->fetchRow($get_bank_card);
    assign('bank_card', $bank_card);
}

if( 'list' == $act ) {
    $get_bank_card_list = 'select * from '.$db->table('bank_card').' where account =\''.$_SESSION['account'].'\'';
    $bank_card_list = $db->fetchAll($get_bank_card_list);

    if( $bank_card_list ) {
        foreach( $bank_card_list as $key => $bank_card ) {
            $bank_card_list[$key]['mobile'] = substr($bank_card['mobile'], 0, 3).'****'.substr($bank_card['mobile'], -4);
            $bank_card_list[$key]['bank_card'] = '尾号'.substr($bank_card['bank_card'], -4);
        }
    }
    assign('bank_card_list', $bank_card_list);
}
assign('act', $act);
$template = 'bank.phtml';
$smarty->display($template);

