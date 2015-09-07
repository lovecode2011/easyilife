<?php
/**
 * 主营行业管理
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-09-06
 * @version 1.0.0
 */

include 'library/init.inc.php';
back_base_init();

$template = 'industry/';
assign('subTitle', '主营行业');

$action = 'edit|add|view|delete';
$operation = 'edit|add';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));
//===========================================================================

if( 'add' == $opera ) {
    if( !check_purview('pur_industry_add', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'edit' == $opera ) {
    if( !check_purview('pur_industry_edit', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}


//===========================================================================

if( 'view' == $act ) {
    if( !check_purview('pur_industry_view', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'add' == $act ) {
    if( !check_purview('pur_industry_add', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'edit' == $act ) {
    if( !check_purview('pur_industry_edit', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}

if( 'delete' == $act ) {
    if( !check_purview('pur_industry_del', $_SESSION['purview']) ) {
        show_system_message('权限不足', array());
        exit;
    }
}


$template .= $act.'.phtml';
$smarty->display($template);