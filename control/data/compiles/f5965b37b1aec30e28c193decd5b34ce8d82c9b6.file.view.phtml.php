<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:42
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/category/view.phtml" */ ?>
<?php /*%%SmartyHeaderCode:201780185255fa97f6e3b1d4-46515814%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f5965b37b1aec30e28c193decd5b34ce8d82c9b6' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/category/view.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '201780185255fa97f6e3b1d4-46515814',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category_list' => 0,
    'category' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa97f6ebf948_07639439',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97f6ebf948_07639439')) {function content_55fa97f6ebf948_07639439($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left">分类列表</h5>
        <div class="pull-right">
            <a href="?act=add" class="btn btn-primary">添加分类</a>
        </div>
        <div class="clear"></div>
    </div>
    <div class="article-main">
        <table class="table">
            <thead>
            <tr>
                <th>分类名称</th>
                <th class="text-right">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($_smarty_tpl->tpl_vars['category_list']->value){?>
            <?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['category_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value){
$_smarty_tpl->tpl_vars['category']->_loop = true;
?>
            <tr id="cate-<?php echo $_smarty_tpl->tpl_vars['category']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['category']->value['parent_id']!=0){?>class="children-<?php echo $_smarty_tpl->tpl_vars['category']->value['parent_id'];?>
 children" style="display: "<?php }else{ ?>class="top"<?php }?>>
                <td class="cate-name" style="cursor: pointer;"><?php echo $_smarty_tpl->tpl_vars['category']->value['name'];?>
</td>
                <td class="text-right"><a href="?act=edit&id=<?php echo $_smarty_tpl->tpl_vars['category']->value['id'];?>
">编辑</a> | <a onclick="javascript:if(confirm('确认要删除？')) return true; else return false;" href="?act=delete&id=<?php echo $_smarty_tpl->tpl_vars['category']->value['id'];?>
">删除</a></td>
            </tr>
            <?php } ?>
            <?php }else{ ?>
            <tr>
                <td style="padding: 50px 0" colspan="2" align="center">您还没有主营分类，立即点击 “<a href="?act=add" target="_blank">这里</a>” 添加栏目！</td>
            </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
</div>
<!-- END content -->

<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>