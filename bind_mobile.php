<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/15
 * Time: 下午4:49
 */
include 'library/init.inc.php';

$operation = 'bind';
$opera = check_action($operation, getPOST('opera'));

if('bind' == $opera)
{
    $response = array('error'=>1, 'msg'=>'');
    $verify = getPOST('verify');
    $mobile = getPOST('mobile');

    if(!check_cross_domain())
    {
        if($mobile == '')
        {
            $response['msg'] .= '-请输入手机号码<br/>';
        } else {
            $mobile = $db->escape($mobile);
        }

        if($verify == '')
        {
            $response['msg'] .= '-请输入验证码<br/>';
        } else {
            if($verify != '123456')
            {
                $response['msg'] .= '-验证码错误<br/>';
            }
        }

        if($response['msg'] == '')
        {
            $data = array('mobile'=>$mobile);

            if($db->autoUpdate('member', $data, '`account`=\''.$_SESSION['account'].'\''))
            {
                $response['error'] = 0;
                $response['msg'] = '绑定手机号码成功';
            } else {
                $response['msg'] ='001:系统繁忙，请稍后再试';
            }
        }
    } else {
        $response['msg'] = '404:参数错误';
    }

    echo json_encode($response);
    exit;
}

$smarty->display('bind_mobile.phtml');