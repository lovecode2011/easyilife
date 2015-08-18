<?php
/**
 * 会员管理
 * Created by PhpStorm.
 * User: apple
 * Date: 15/8/14
 * Time: 下午10:04
 */
include 'library/init.inc.php';
back_base_init();

$template = 'member/';
assign('subTitle', '会员管理');

$action = 'edit|add|view|delete';
$operation = 'edit|add';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));
//===========================================================================
//会员充值
if('recharge' == $opera)
{
}

//修改会员信息
if('edit' == $opera)
{
}

//锁定账户
if('lock' == $opera)
{
}

//解锁账户
if('unlock' == $opera)
{
}

//删除会员
if('delete' == $opera)
{
}

assign('act', $act);
$smarty->display($template.'member.phtml');