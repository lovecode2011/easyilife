<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:35:34
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/withdraw/log.phtml" */ ?>
<?php /*%%SmartyHeaderCode:207108457955fa97769a3151-17915349%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '528f623d744bea6b4964bff08798fdd9ec9e45ce' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/withdraw/log.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '207108457955fa97769a3151-17915349',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'type' => 0,
    'count' => 0,
    'st' => 0,
    'et' => 0,
    'log_list' => 0,
    'log' => 0,
    'total' => 0,
    'totalPage' => 0,
    'go_first' => 0,
    'has_prev' => 0,
    'page' => 0,
    'has_many_prev' => 0,
    'show_page' => 0,
    'pageNum' => 0,
    'has_many_next' => 0,
    'go_last' => 0,
    'has_next' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa9776b25502_88937189',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa9776b25502_88937189')) {function content_55fa9776b25502_88937189($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left"><?php if ($_smarty_tpl->tpl_vars['type']->value==0){?>会员-<?php }else{ ?>商户-<?php }?>提现记录</h5>
        <div class="pull-right">
            <a href="withdraw.php?act=log&type=0" class="btn btn-primary">会员</a>
            <a href="withdraw.php?act=log&type=1" class="btn btn-primary">商户</a>
            <a href="withdraw.php" class="btn btn-primary">返回</a>
        </div>
        <div class="clear"></div>
    </div>
    <div class="article-main-header">
        <form action="" method="get" />
        <fieldset>
            <p>
            <div class="pull-left">
                显示
                <select name="count" class="w75">
                    <option value="10" <?php if ($_smarty_tpl->tpl_vars['count']->value==10){?>selected="selected"<?php }?>>10</option>
                    <option value="25" <?php if ($_smarty_tpl->tpl_vars['count']->value==25){?>selected="selected"<?php }?>>25</option>
                    <option value="50" <?php if ($_smarty_tpl->tpl_vars['count']->value==50){?>selected="selected"<?php }?>>50</option>
                    <option value="100" <?php if ($_smarty_tpl->tpl_vars['count']->value==100){?>selected="selected"<?php }?>>100</option>
                </select>
                项结果
            </div>
            <div class="pull-right">
                <label>起始日期：</label>
                <input class="text-input w150" type="text" id="" name="st" onClick="laydate({istime: true, format: 'YYYY-MM-DD'})" value="<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
">&nbsp;
                <label>结束日期：</label>
                <input class="text-input w150" type="text" id="" name="et" onClick="laydate({istime: true, format: 'YYYY-MM-DD'})" value="<?php echo $_smarty_tpl->tpl_vars['et']->value;?>
">&nbsp;
                <button type="submit" class="btn btn-default">搜索</button>
                <input type="hidden" name="type" value="<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
"/>
            </div>
            </p>
        </fieldset>
        </form>
        <div class="clear"></div>
    </div>
    <div class="withdraw-record">
        <table class="table">
            <thead>
            <tr>
                <th>流水号</th>
                <th>操作时间</th>
                <th>是否到帐</th>
                <th>操作员</th>
                <th>备注</th>
            </tr>
            </thead>
            <tbody>
            <?php  $_smarty_tpl->tpl_vars['log'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['log']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['log_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['log']->key => $_smarty_tpl->tpl_vars['log']->value){
$_smarty_tpl->tpl_vars['log']->_loop = true;
?>
            <tr>
                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['withdraw_sn'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['add_time_str'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['status_str'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['operator'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['remark'];?>
</td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="5">
                    <div class="pull-right">
                        <div class="pages">
                            <span>共有<?php echo $_smarty_tpl->tpl_vars['total']->value;?>
条，<?php echo $_smarty_tpl->tpl_vars['totalPage']->value;?>
页，每页显示：<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
条</span>
                            <?php if ($_smarty_tpl->tpl_vars['go_first']->value){?>
                            <a href="?page=1&st=<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
&et=<?php echo $_smarty_tpl->tpl_vars['et']->value;?>
&type=<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
">首页</a>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['has_prev']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['page']->value-1;?>
&st=<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
&et=<?php echo $_smarty_tpl->tpl_vars['et']->value;?>
&type=<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
">上一页</a>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['has_many_prev']->value){?>
                            ...
                            <?php }?>
                            <?php  $_smarty_tpl->tpl_vars['pageNum'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pageNum']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['show_page']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pageNum']->key => $_smarty_tpl->tpl_vars['pageNum']->value){
$_smarty_tpl->tpl_vars['pageNum']->_loop = true;
?>
                            <?php if ($_smarty_tpl->tpl_vars['pageNum']->value==$_smarty_tpl->tpl_vars['page']->value){?>
                            <b><?php echo $_smarty_tpl->tpl_vars['page']->value;?>
</b>
                            <?php }else{ ?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['pageNum']->value;?>
&st=<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
&et=<?php echo $_smarty_tpl->tpl_vars['et']->value;?>
&type=<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['pageNum']->value;?>
</a>
                            <?php }?>
                            <?php } ?>
                            <?php if ($_smarty_tpl->tpl_vars['has_many_next']->value){?>
                            ...
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['go_last']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['totalPage']->value;?>
&st=<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
&et=<?php echo $_smarty_tpl->tpl_vars['et']->value;?>
&type=<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
">末页</a>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['has_next']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['page']->value+1;?>
&st=<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
&et=<?php echo $_smarty_tpl->tpl_vars['et']->value;?>
&type=<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
">下一页</a>
                            <?php }?>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>


</div>
<!-- END content -->
<!-- 日历控件 -->
<script type="text/javascript" src="laydate/laydate.js"></script>
<script>
    
    $(function(){
        laydate.skin('yalan');//切换皮肤，请查看skins下面皮肤库
    });
    
</script>
<!-- END 日历控件 -->

<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>