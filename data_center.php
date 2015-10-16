<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/23
 * Time: 下午3:07
 */
include 'library/init.inc.php';

$operation = 'get_fav|verify_pic_code|verify_message_code|get_message_code';
$opera = check_action($operation, getPOST('opera'));

$response = array('error'=>1, 'msg'=>'');

if('verify_message_code' == $opera)
{
    $mobile = getPOST('mobile');
    $code = getPOST('code');

    if(is_mobile($mobile) && isset($_SESSION['token']))
    {
        $mobile = $db->escape($mobile);
        //检查验证码池是否存在该手机
        $check_code = 'select `code`,`expire` from '.$db->table('message_code').' where `mobile`=\''.$mobile.'\'';
        $message_code = $db->fetchRow($check_code);

        if($code != $message_code['code'])
        {
            $response['msg'] = '短信验证码错误';
        } else {
            $response['msg'] = '验证码校验通过';
            $response['error'] = 0;
            $_SESSION['token'] = 'verify message code success.';
        }
    } else {
        $response['msg'] = '手机号码格式不正确';
    }
}

if('get_message_code' == $opera)
{
    $mobile = getPOST('mobile');

    if(is_mobile($mobile) && isset($_SESSION['token']))
    {
        $mobile_code = rand(100000, 999999);

        $mobile = $db->escape($mobile);
        //检查验证码池是否存在该手机
        $check_code = 'select `code`,`expire` from '.$db->table('message_code').' where `mobile`=\''.$mobile.'\'';
        $message_code = $db->fetchRow($check_code);

        if($message_code && $message_code['expire'] >= time())
        {
            //验证码存在且未过期
            $response['msg'] = '请不要重复获取验证码';
            $response['timer'] = $message_code['expire'] - time();
            $_SESSION['mobile_code'] = $message_code['code'];
        } else {
            if(sendSMS($mobile, $mobile_code))
            {
                //验证码发送成功
                if($message_code)
                {
                    $data = array(
                        'code' => $mobile_code,
                        'expire' => time()+60
                    );

                    if($db->autoUpdate('message_code', $data))
                    {
                        $response['error'] = 0;
                        $response['timer'] = 60;
                        $_SESSION['mobile_code'] = $mobile_code;
                        $response['code'] = $mobile_code;
                        $_SESSION['token'] = 'send message code success.';
                    } else {
                        $response['msg'] = '001:参数错误';
                    }
                } else {
                    $data = array(
                        'code' => $mobile_code,
                        'expire' => time()+60,
                        'mobile' => $mobile
                    );

                    if($db->autoInsert('message_code', array($data)))
                    {
                        $response['error'] = 0;
                        $response['timer'] = 60;
                        $_SESSION['mobile_code'] = $mobile_code;
                        $response['code'] = $mobile_code;
                        $_SESSION['token'] = 'send message code success.';
                    } else {
                        $response['msg'] = '002:参数错误';
                    }
                }
            } else {
                $response['msg'] = '获取验证码失败,系统繁忙,请稍后再试';
            }
        }
    } else {
        $response['msg'] = '手机号码格式不正确';
    }
}

if('verify_pic_code' == $opera)
{
    $code = getPOST('code');
    $code = strtolower($code);

    if($code != $_SESSION['code'])
    {
        $response['msg'] = '图片验证码错误';
    } else {
        $response['error'] = 0;
    }
}

if('get_fav' == $opera)
{
    $get_fav_products = 'select `name`,`price`,`img`,`id` from '.$db->table('product').' order by `add_time` DESC limit 3';
    $fav_products = $db->fetchAll($get_fav_products);
    assign('product_list', $fav_products);

    $response['error'] = 0;
    $response['content'] = $smarty->fetch('product-item.phtml');
}

echo json_encode($response);
exit;