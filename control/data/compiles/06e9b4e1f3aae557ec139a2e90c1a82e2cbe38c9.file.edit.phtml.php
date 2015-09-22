<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:34:35
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/member/edit.phtml" */ ?>
<?php /*%%SmartyHeaderCode:122882711655fa973ba28ca1-66908020%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '06e9b4e1f3aae557ec139a2e90c1a82e2cbe38c9' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/member/edit.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '122882711655fa973ba28ca1-66908020',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'member' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa973ba7a9f8_04140161',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa973ba7a9f8_04140161')) {function content_55fa973ba7a9f8_04140161($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left">编辑会员-<?php echo $_smarty_tpl->tpl_vars['member']->value['account'];?>
</h5>
        <!--        <div class="pull-right"><a class="btn btn-primary" href="?act=passwd">修改密码</a></div>-->
        <div class="clear"></div>
    </div>
    <div class="basicInfo-main">
        <form id="memberEditForm" method="post" action="">
            <fieldset>
                <p>
                    <label class="l-title">手机：</label>
                    <input class="text-input w300" type="text" maxlength="64" id="mobile" name="mobile" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['mobile'];?>
" />
                </p>
                <p>
                    <label class="l-title">佣金：</label>
                    <input class="text-input w300" type="text" maxlength="64" id="reward" name="reward" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['reward'];?>
" />
                </p>
                <p>
                    <label class="l-title">积分：</label>
                    <input class="text-input w300" type="text" maxlength="64" id="integral" name="integral" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['integral'];?>
" />
                </p>
                <p>
                    <label class="l-title"></label>
                    <button class="btn btn-primary" type="submit">保存</button>
                    <a class="btn btn-primary" href="member.php">返回</a>
                    <input type="hidden" name="opera" value="edit" />
                    <input type="hidden" name="account" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['account'];?>
" />
                </p>

            </fieldset>
        </form>
    </div>
</div>
<!-- END content -->
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>