<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:33:49
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/index/login.phtml" */ ?>
<?php /*%%SmartyHeaderCode:47084821755fa970d4b5197-85191955%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1edb55b0818ad3dce9e1aad534298d4ddef7548d' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/index/login.phtml',
      1 => 1440508153,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '47084821755fa970d4b5197-85191955',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pageTitle' => 0,
    'error' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa970d502268_08088140',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa970d502268_08088140')) {function content_55fa970d502268_08088140($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $_smarty_tpl->tpl_vars['pageTitle']->value;?>
</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
    <!--[if IE 6]><script src="js/DDbelatedPNG.js"></script><script>DD_belatedPNG.fix('.png');</script><![endif]-->
    <script type="text/javascript" src="js/jquery.js"></script>
</head>
<body>
<form name="admin-login-form" method="post" >
    帐号：<input type="text" name="account" />
    <span><?php if (isset($_smarty_tpl->tpl_vars['error']->value['account'])){?><?php echo $_smarty_tpl->tpl_vars['error']->value['account'];?>
<?php }?></span>
    密码：<input type="password" name="password" />
    <span><?php if (isset($_smarty_tpl->tpl_vars['error']->value['password'])){?><?php echo $_smarty_tpl->tpl_vars['error']->value['password'];?>
<?php }?></span>
    <input type="submit" name="login" value="登陆" />
    <input type="hidden" name="opera" value="login" />
</form>
</body>
</html><?php }} ?>