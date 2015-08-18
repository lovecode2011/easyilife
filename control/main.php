<?php
/**
 * 管理后台首页
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-8-5
 * @version 1.0.0
 */

include 'library/init.inc.php';

//管理后台初始化
back_base_init();
$template = 'main/';

$action = 'view';
$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;


$template .= $act.'.phtml';
$smarty->display($template);




