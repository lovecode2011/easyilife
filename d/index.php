<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/16
 * Time: 下午2:04
 */
include 'library/init.inc.php';

$code = $_GET['code'];
if($code != '')
{
    $url = $db->fetchOne('select `url` from '.$db->table('short_link').' where `hash`=\''.$code.'\'');
    //header('Location: '.$url);
    print_r($_GET);
    exit;
}

$act = getGET('act');
if($act == 'add')
{
    $url = 'product.php?id=1&code=osi_wxyhonsdfljlo';
    $hash = get_hash_map();

    $data = array(
        'url' => $url,
        'hash' => $hash
    );

    $db->autoInsert('short_link', array($data));
}

function get_hash_map()
{
    global $db;

    $seed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $strlen = strlen($seed)-1;
    $hashlen = 5;

    $hash = '';

    do
    {
        $count = $hashlen;
        while($count--)
        {
            $index = rand(0, $strlen);
            $hash .= $seed[$index];
        }
        $check_hash = 'select `url` from '.$db->table('short_link').' where `hash`=\''.$hash.'\'';
    } while($db->fetchOne($check_hash));

    return $hash;
}
