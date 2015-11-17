<?php
/**
 * 首页
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:11
 */
include 'library/init.inc.php';

if( $is_login ) {
    redirect('home.php');
}
$smarty->display('login.phtml');