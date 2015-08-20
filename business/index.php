<?php
/**
 * 商户管理后台登陆页
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-8-19
 * @version 1.0.0
 */
include 'library/init.inc.php';

$template = 'index/';

$action = 'login|forget|logout';
$operation = 'login|forget';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'login' : $act;

$opera = check_action($operation, getPOST('opera'));

$error = array();

//登陆
if( 'login' == $opera ) {
    $account = trim(getPOST('account'));
    $password = trim(getPOST('password'));

    if('' == $account)
    {
        $error['account'] = '账号不能为空';
    } else {
        $account = $db->escape(htmlspecialchars($account));
    }

    if('' == $password)
    {
        $error['password'] = '密码不能为空';
    } else {
        $password = md5($password.PASSWORD_END);
    }
    $checkAccount = 'select `password`,`shop_name` from '.$db->table('business').' where `business_account`=\''.$account.'\' limit 1';
    $business = $db->fetchRow($checkAccount);

    if($business)
    {
        if($password == $business['password'])
        {
            $_SESSION['business_shop_name'] = $business['shop_name'];
            $_SESSION['business_account'] = $account;

            show_system_message('登录成功', array(array('alt'=>'进入管理后台', 'link'=>'main.php')));
            exit;

        } else {
            $error['password'] = '密码错误';
        }
    } else {
        $error['account'] = '账号不存在';
    }
}

//忘记密码
if( 'forget' == $opera ) {

}

//登陆，默认
if( 'login' == $act ) {
    //如果已登陆
    if( check_admin_login() ) {
        redirect('main.php');
    }
}
//忘记密码
if( 'forget' == $act ) {
    //如果已登陆
    if( check_admin_login() ) {
        redirect('self.php');
    }
}

//注销
if( 'logout' == $act ) {
    unset($_SESSION['business_shop_name']);
    unset($_SESSION['business_account']);

    redirect('index.php');
}

assign('error', $error);

assign('pageTitle', '商户管理后台');
$template .= $act.'.phtml';
$smarty->display($template);

