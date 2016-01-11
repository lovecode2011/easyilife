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

$openid = $db->escape($openid);
$get_user_info = 'select * from '.$db->table('member').' where `openid`=\''.$openid.'\'';
$user_info = $db->fetchRow($get_user_info);

assign('user_info', $user_info);

$img_qrcode = ROOT_PATH . 'upload/recommend/' . $user_info['id'] . '.png';

if($user_info['expired'] <= time())
{
    $access_token = get_access_token($config['appid'], $config['appsecret']);
    $qrcode = get_qrcode($user_info['openid'], $access_token);

    if ($qrcode) {
        assign('qrcode', $qrcode);
    } else {
        assign('qrcode', false);
        echo '系统繁忙，请刷新一下';
        exit;
    }

    $source = $user_info['headimg'];
    $dest = '../themes/' . $config['themes'] . '/images/qr_bg.jpg';

    $img_qrcode = image_merge($source, $dest, $qrcode, $user_info['nickname'], $img_qrcode);
}

if($img_qrcode)
{
    assign('qrcode', str_replace(ROOT_PATH, '../', $img_qrcode));
} else {
    echo '系统繁忙';
}
$smarty->display('qr-card.phtml');