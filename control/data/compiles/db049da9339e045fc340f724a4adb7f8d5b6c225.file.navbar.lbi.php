<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:52
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/library/navbar.lbi" */ ?>
<?php /*%%SmartyHeaderCode:131854937955fa9800ce0ab5-58714688%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'db049da9339e045fc340f724a4adb7f8d5b6c225' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/library/navbar.lbi',
      1 => 1440508153,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '131854937955fa9800ce0ab5-58714688',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currentAdmin' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa9800ce9b30_55475710',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa9800ce9b30_55475710')) {function content_55fa9800ce9b30_55475710($_smarty_tpl) {?><!-- navbar -->
<div class="navbar">
    <div class="navbar-inner">
        <div class="navbar-left"><a href="#"><img class="png" src="images/logo.png" alt="logo"/></a></div>
        <div class="navbar-right">
            <a href="#" class="navbar-member"><em><?php echo $_smarty_tpl->tpl_vars['currentAdmin']->value;?>
</em><span class="icon">&#xe609;</span></a>
            <ul id="dropdown-menu" style="display:none;">
                <li class="topbar-info-btn"><a href="index.php?act=logout">注销</a></li>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
</div>
<!-- END navbar --><?php }} ?>