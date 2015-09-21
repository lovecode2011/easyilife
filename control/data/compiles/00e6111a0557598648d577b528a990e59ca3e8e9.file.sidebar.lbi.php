<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:52
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/library/sidebar.lbi" */ ?>
<?php /*%%SmartyHeaderCode:60328303555fa9800cedb03-70795467%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '00e6111a0557598648d577b528a990e59ca3e8e9' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/library/sidebar.lbi',
      1 => 1439774040,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '60328303555fa9800cedb03-70795467',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'menus' => 0,
    'menu' => 0,
    'child' => 0,
    'is_main' => 0,
    'menuMark' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa9800d88856_21774795',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa9800d88856_21774795')) {function content_55fa9800d88856_21774795($_smarty_tpl) {?><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<!-- sidebar -->
<div id="sidebar">
    <div id="sidebar-wrapper">
        <ul id="main-nav">
            <!-- main Menu -->
            <li class="coin_1">
                <a href="main.php" class="nav-top-item">
                    <em class="icon">&#xe606;</em>首页
                </a>
            </li>
            <?php  $_smarty_tpl->tpl_vars['menu'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['menu']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menus']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['menu']->key => $_smarty_tpl->tpl_vars['menu']->value){
$_smarty_tpl->tpl_vars['menu']->_loop = true;
?>
            <li class="coin_<?php echo $_smarty_tpl->tpl_vars['menu']->value['key'];?>
">
                <?php if ($_smarty_tpl->tpl_vars['menu']->value['count']==1){?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['menu']->value['url'];?>
" class="nav-top-item" >
                    <em class="icon"><?php echo $_smarty_tpl->tpl_vars['menu']->value['icon'];?>
</em><?php echo $_smarty_tpl->tpl_vars['menu']->value['title'];?>

                </a>
                <?php }else{ ?>
                <a href="<?php echo $_smarty_tpl->tpl_vars['menu']->value['url'];?>
" class="nav-top-item">
                    <em class="icon"><?php echo $_smarty_tpl->tpl_vars['menu']->value['icon'];?>
</em><?php echo $_smarty_tpl->tpl_vars['menu']->value['title'];?>

                </a>
                <ul class="submenu" id="<?php echo $_smarty_tpl->tpl_vars['menu']->value['key'];?>
" style="display: none">
                    <?php  $_smarty_tpl->tpl_vars['child'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['child']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menu']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['child']->key => $_smarty_tpl->tpl_vars['child']->value){
$_smarty_tpl->tpl_vars['child']->_loop = true;
?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['child']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['child']->value['title'];?>
</a></li>
                    <?php } ?>
                </ul>
                <?php }?>
            </li>

            <?php } ?>
        </ul> <!-- End #main-nav -->
    </div>
</div>
<!-- END sidebar -->
<?php if ($_smarty_tpl->tpl_vars['is_main']->value==false){?>
<script type="text/javascript">
    $(function () {
        <?php if ($_smarty_tpl->tpl_vars['menuMark']->value['count']>1){?>
        menu1("#<?php echo $_smarty_tpl->tpl_vars['menuMark']->value['name'];?>
", "#<?php echo $_smarty_tpl->tpl_vars['menuMark']->value['name'];?>
 a", ".coin_<?php echo $_smarty_tpl->tpl_vars['menuMark']->value['name'];?>
 a.nav-top-item");
        <?php }else{ ?>
        menu1("", "", ".coin_<?php echo $_smarty_tpl->tpl_vars['menuMark']->value['name'];?>
 a.nav-top-item");
        <?php }?>
    })
</script>
<?php }else{ ?>
<script type="text/javascript">
    $(function () {
        menu1("", "", ".coin_1 a.nav-top-item");
    })
</script>

<?php }?>

<?php }} ?>