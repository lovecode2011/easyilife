<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:04
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/admin/view.phtml" */ ?>
<?php /*%%SmartyHeaderCode:180441626255fa97d07f1139-28872794%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be92eea25fa4762789a8d48f55b25cdbb732c178' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/admin/view.phtml',
      1 => 1440508153,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '180441626255fa97d07f1139-28872794',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'subTitle' => 0,
    'adminList' => 0,
    'admin' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa97d08baa49_42913439',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97d08baa49_42913439')) {function content_55fa97d08baa49_42913439($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left"><?php echo $_smarty_tpl->tpl_vars['subTitle']->value;?>
</h5>
        <div class="pull-right"><a class="btn btn-primary" href="?act=add">添加用户</a></div>
        <div class="clear"></div>
    </div>
    <div class="adminUser-main">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>账号</th>
                <th>昵称</th>
                <th>邮件</th>
<!--                <th>手机</th>-->
                <th>角色</th>
                <th class="text-right">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php  $_smarty_tpl->tpl_vars['admin'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['admin']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['adminList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['admins']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['admin']->key => $_smarty_tpl->tpl_vars['admin']->value){
$_smarty_tpl->tpl_vars['admin']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['admins']['iteration']++;
?>
            <tr>
                <td><?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['admins']['iteration'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['admin']->value['account'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['admin']->value['name'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['admin']->value['email'];?>
</td>
<!--                <td><?php echo $_smarty_tpl->tpl_vars['admin']->value['mobile'];?>
</td>-->
                <td><?php echo $_smarty_tpl->tpl_vars['admin']->value['role_name'];?>
</td>
                <?php if ($_smarty_tpl->tpl_vars['admin']->value['role_id']!=1){?>
                <td class="text-right"><a href="?act=edit&account=<?php echo $_smarty_tpl->tpl_vars['admin']->value['account'];?>
">编辑</a> | <a onclick="javascript:if(confirm('确认要删除？')) return true; else return false;" href="?act=delete&account=<?php echo $_smarty_tpl->tpl_vars['admin']->value['account'];?>
">删除</a></td>
                <?php }else{ ?>
                <td class="text-right"></td>

                <?php }?>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<!-- END content -->
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>