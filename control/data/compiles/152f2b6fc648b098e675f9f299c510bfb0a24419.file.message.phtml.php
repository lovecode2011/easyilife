<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:35:15
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/public/message.phtml" */ ?>
<?php /*%%SmartyHeaderCode:146102039955fa97637d5fe3-27018792%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '152f2b6fc648b098e675f9f299c510bfb0a24419' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/public/message.phtml',
      1 => 1440508153,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '146102039955fa97637d5fe3-27018792',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'time' => 0,
    'link' => 0,
    'page_title' => 0,
    'message' => 0,
    'links' => 0,
    'a' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa976383c0a9_16047663',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa976383c0a9_16047663')) {function content_55fa976383c0a9_16047663($_smarty_tpl) {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml-transitional.dtd"><html><head>    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>	<meta http-equiv="Refresh" content="<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
; url=<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
"/>	<title><?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
</title>    <link rel="stylesheet" type="text/css" href="css/style.css"/></head><body><div id="content">    <?php echo $_smarty_tpl->tpl_vars['message']->value;?>
    <?php if ($_smarty_tpl->tpl_vars['links']->value){?>    <?php  $_smarty_tpl->tpl_vars['a'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['a']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['a']->key => $_smarty_tpl->tpl_vars['a']->value){
$_smarty_tpl->tpl_vars['a']->_loop = true;
?>        <a href="<?php echo $_smarty_tpl->tpl_vars['a']->value['link'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['a']->value['alt'];?>
"><?php echo $_smarty_tpl->tpl_vars['a']->value['alt'];?>
</a>    <?php } ?>    <?php }?></div><?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</body></html><?php }} ?>