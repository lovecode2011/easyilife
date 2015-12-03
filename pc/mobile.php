<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-12-3
 * @version 
 */
include 'library/init.inc.php';

$operation = 'edit|verify';

$opera = check_action($operation, getPOST('opera'));

if( 'verify' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    if(!check_cross_domain() && isset($_SESSION['account']) && $_SESSION['account']) {
        $code = getPOST('code');
        $password = trim(getPOST('password'));

        $code = getPOST('code');
        $code = strtolower($code);

        if($code != $_SESSION['code'])
        {
            $response['msg'] .= '-图片验证码错误<br />';
        } else {
            $get_password = 'select password from '.$db->table('member').' where account = \''.$_SESSION['account'].'\' limit 1';
            $cur_password = $db->fetchOne($get_password);
            if( md5($password.PASSWORD_END) == $cur_password ) {
                $response['error'] = 0;
                $_SESSION['token'] = 'password correct.';
            } else {
                $response['msg'] .= '-密码错误';
            }
        }
    } else {
        if(empty($_SESSION['account']))
        {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }
    echo json_encode($response);
    exit;
}

if('edit' == $opera)
{
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    if(!check_cross_domain() && isset($_SESSION['account']) && $_SESSION['account']) {
        $mobile = getPOST('mobile');
        $code = getPOST('code');
        $ref = getPOST('ref');

        if( !(isset($_SESSION['token']) && $_SESSION['token'] == 'send message code success.') ) {
            $response['msg'] = '请先通过身份验证';
            echo json_encode($response);
            exit;
        }
        if(is_mobile($mobile))
        {
            $mobile = $db->escape($mobile);
            //检查验证码池是否存在该手机
            $check_code = 'select `code`,`expire` from '.$db->table('message_code').' where `mobile`=\''.$mobile.'\'';
            $message_code = $db->fetchRow($check_code);

            if($code != $message_code['code'])
            {
                $response['msg'] = '短信验证码错误';
            } else {

                $mobile = $db->escape($mobile);

                $data = array(
                    'mobile' => $mobile
                );
                $get_user_info = 'select * from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
                $user_info = $db->fetchRow($get_user_info);
                if( $user_info['mobile'] == $user_info['nickname'] ) {
                    $data['nickname'] = $mobile;
                }

                if($db->autoUpdate('member', $data, '`account`=\''.$_SESSION['account'].'\''))
                {
                    $response['msg'] = '修改手机号码成功';
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
        } else {
            $response['msg'] = '手机号码格式不正确';
        }
    } else {
        if(empty($_SESSION['account']))
        {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }

    echo json_encode($response);
    exit;
}
$_SESSION['token'] = 'can send message.';
$get_mobile = 'select `mobile` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$mobile = $db->fetchOne($get_mobile);
assign('mobile', $mobile);
$smarty->display('mobile.phtml');