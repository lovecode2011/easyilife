<?php
/**
 * 商户产品管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-08-19
 * @version 1.0.0
 */

include 'library/init.inc.php';

//商户管理后台初始化
business_base_init();
$template = 'product/';

$action = 'view|add|edit|delete|cycle|revoke|remove';
$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

if( 'view' == $act ) {

}

if( 'add' == $act ) {

}

if( 'edit' == $act ) {

}

if( 'delete' == $act ) {

}

if( 'cycle' == $act ) {

}

if( 'revoke' == $act ) {

}

if( 'remove' == $act ) {

}


$template .= $act.'.phtml';
$smarty->display($template);

