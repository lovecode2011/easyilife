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

    $email = getPOST('email');
    $sex = getPOST('sex');

    if($email == '')
    {
        $response['msg'] .= '-请填写邮箱地址<br/>';
    } else {
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $email = $db->escape($email);
        } else {
            $response['msg'] .= '-邮箱格式不正确<br/>';
        }
    }

    $sex_list = 'N|F|M';
    $sex = check_action($sex_list, $sex);

    if($sex == '')
    {
        $sex = 'N';
    }

    if($response['msg'] == '')
    {
        $member_data = array(
            'email' => $email,
            'sex' => $sex
        );

        if($db->autoUpdate('member', $member_data, '`account`=\''.$_SESSION['account'].'\''))
        {
            $response['error'] = 0;
            $response['msg'] = '修改信息成功';
        } else {
            $response['msg'] = '系统繁忙，请稍后再试';
        }
    }

    echo json_encode($response);
    exit;
}

$level = array(
    0 => '普通会员',
    1 => '合伙人',
    2 => '高级合伙人'
);
assign('level', $level);

$get_user_info = 'select `level_id`,`nickname`,`mobile`,`account`,`sex`,`email` from '.$db->table('member').' where `account`=\''.$_SESSION['account'].'\'';
$user = $db->fetchRow($get_user_info);
assign('user', $user);

$smarty->display('user-info.phtml');
