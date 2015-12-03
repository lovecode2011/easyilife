<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/7
 * Time: 上午11:22
 */
include 'library/init.inc.php';

$operation = 'edit';

$opera = check_action($operation, getPOST('opera'));

if('edit' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');

    $password = getPOST('password');
    $ref = getPOST('ref');
    if($password == '')
    {
        $response['msg'] = '请填写新密码';
    }

    if(!isset($_SESSION['token']) || $_SESSION['token'] != 'verify message code success.')
    {
        $response['msg'] = '请先通过身份验证';
    }

    if($response['msg'] == '')
    {
        $password = md5($password.PASSWORD_END);

        $data = array(
            'password' => $password
        );

        if($db->autoUpdate('member', $data, '`account`=\''.$_SESSION['account'].'\''))
        {
            $response['msg'] = '修改密码成功';
            $response['error'] = 0;
            if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'] ,'login.php') === false)
            {
                $response['referer'] = $_SERVER['HTTP_REFERER'];
            } else {
                $response['referer'] = 'index.php';
            }
            $response['referer'] = $ref;
            unset($_SESSION['token']);
        } else {
            $response['msg'] = '系统繁忙，请稍后再试';
        }
    }

    echo json_encode($response);
    exit;
}

$_SESSION['token'] = 'can send message.';
$get_mobile = 'select `mobile` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$mobile = $db->fetchOne($get_mobile);
assign('mobile', $mobile);

$smarty->display('password.phtml');