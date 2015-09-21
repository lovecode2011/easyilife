<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:32
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/industry/view.phtml" */ ?>
<?php /*%%SmartyHeaderCode:212807624955fa97ec782ab6-78607039%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '01a17ab6a0b980e55350e22366a572b70a959ccd' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/industry/view.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '212807624955fa97ec782ab6-78607039',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'industry_list' => 0,
    'industry' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa97ec808f02_54505146',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97ec808f02_54505146')) {function content_55fa97ec808f02_54505146($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left">主营行业</h5>
        <div class="pull-right">
            <a href="?act=add" class="btn btn-primary">添加行业</a>
        </div>
        <div class="clear"></div>
    </div>
    <div class="article-main">
        <table class="table">
            <thead>
            <tr>
                <th>行业名称</th>
                <th class="text-right">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($_smarty_tpl->tpl_vars['industry_list']->value){?>
            <?php  $_smarty_tpl->tpl_vars['industry'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['industry']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['industry_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['industry']->key => $_smarty_tpl->tpl_vars['industry']->value){
$_smarty_tpl->tpl_vars['industry']->_loop = true;
?>
            <tr>
                <td><?php echo $_smarty_tpl->tpl_vars['industry']->value['name'];?>
</td>
                <td class="text-right"><a href="?act=edit&id=<?php echo $_smarty_tpl->tpl_vars['industry']->value['id'];?>
">编辑</a> | <a onclick="javascript:if(confirm('确认要删除？')) return true; else return false;" href="?act=delete&id=<?php echo $_smarty_tpl->tpl_vars['industry']->value['id'];?>
">删除</a></td>
            </tr>
            <?php } ?>
            <?php }else{ ?>
            <tr>
                <td style="padding: 50px 0" colspan="2" align="center">您还没有主营行业，立即点击 “<a href="?act=add" target="_blank">这里</a>” 添加行业！</td>
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