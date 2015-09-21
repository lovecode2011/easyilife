<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:33:53
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/main/view.phtml" */ ?>
<?php /*%%SmartyHeaderCode:203016889555fa97113361a0-29135268%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ea173fbbed84b1706ebdf39959e37f937436213b' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/main/view.phtml',
      1 => 1440508153,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '203016889555fa97113361a0-29135268',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa971136c011_48127089',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa971136c011_48127089')) {function content_55fa971136c011_48127089($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left">首页</h5>
        <div class="clear"></div>
    </div>
    <div class="start-main">
        <h3>网站概要</h3>
        <p>目前有 <i>3</i> 篇资讯, 并有 <i>5</i> 个产品, <i>1</i> 个用户.</p>
        <p>点击下面的链接快速开始:</p>
        <ul class="start-link">
            <li><a href="#" target="_blank">添加资讯</a></li>
            <li><a href="#" target="_blank">添加产品</a></li>
            <li><a href="#" target="_blank">用户管理</a></li>
            <li><a href="#" target="_blank">系统设置</a></li>
        </ul>
    </div>
</div>
<!-- END content -->

<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html>
<?php }} ?>