<?php
/**
 * 物流方式设置
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-08-19
 * @version 1.0.0
 */

include 'library/init.inc.php';

//商户管理后台初始化
business_base_init();
$template = 'express/';

$action = 'view|edit|delete';
$operation = 'edit';
$act = check_action($action, getGET('act'));
$opera = check_action($operation, getPOST('opera'));
$act = ( $act == '' ) ? 'view' : $act;
//===============================================================================

//===============================================================================


$template .= $act.'.phtml';
$smarty->display($template);