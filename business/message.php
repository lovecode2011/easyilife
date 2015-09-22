<?php
/**
 * 系统消息
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-09-22
 * @version 1.0.0
 */

include 'library/init.inc.php';
back_base_init();

$template = 'message/';
assign('subTitle', '商户管理');

$action = 'view';
$operation = '';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));
//===========================================================================

//===========================================================================

if( 'view' == $act ) {

}

$template .= $act.'.phtml';
$smarty->display($template);

