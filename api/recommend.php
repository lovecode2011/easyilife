<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/20
 * Time: 上午9:38
 */
include 'library/init.inc.php';

$openid = trim(getGET('ticket'));

if($openid == '')
{
    echo '参数错误';
    exit;
}

$get_user_info = 'select * from '.$db->table('member').' where `openid`=\''.$openid.'\'';
$user_info = $db->fetchRow($get_user_info);

assign('user_info', $user_info);

$access_token = get_access_token($config['appid'], $config['appsecret']);
$qrcode = get_qrcode($user_info['openid'], $access_token);

if($qrcode)
{
    assign('qrcode', $qrcode);
} else {
    assign('qrcode', false);
    echo '系统繁忙，请稍后再试';
}

$smarty->display('qr-card.phtml');