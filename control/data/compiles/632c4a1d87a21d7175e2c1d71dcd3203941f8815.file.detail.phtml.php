<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:36:57
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/account/detail.phtml" */ ?>
<?php /*%%SmartyHeaderCode:201703927855fa97c9d261d1-31315604%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '632c4a1d87a21d7175e2c1d71dcd3203941f8815' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/account/detail.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '201703927855fa97c9d261d1-31315604',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'type' => 0,
    'account' => 0,
    'count' => 0,
    'st' => 0,
    'et' => 0,
    'total' => 0,
    'exchange_list' => 0,
    'exchange' => 0,
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
  'unifunc' => 'content_55fa97ca096fc2_73678983',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97ca096fc2_73678983')) {function content_55fa97ca096fc2_73678983($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left"><?php if ($_smarty_tpl->tpl_vars['type']->value==0){?>会员<?php }else{ ?>商户<?php }?><?php echo $_smarty_tpl->tpl_vars['account']->value;?>
-账户明细</h5>
        <div class="pull-right">
            <a href="javascript:history.go(-1)" class="btn btn-primary">返回</a>
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
                <input type="hidden" name="act" value="detail"/>
                <input type="hidden" name="account" value="<?php echo $_smarty_tpl->tpl_vars['account']->value;?>
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
                <?php if ($_smarty_tpl->tpl_vars['type']->value==0){?>
                <th>帐号</th>
                <th>变动时间</th>
                <th>余额</th>
                <th>积分</th>
                <th>佣金</th>
                <th>待发积分</th>
                <th>待发佣金</th>
                <th>操作员</th>
                <th>备注</th>
                <?php }else{ ?>
                <th>商家帐号</th>
                <th>变动时间</th>
                <th>余额</th>
                <th>担保交易</th>
                <th>操作员</th>
                <th>备注</th>
                <?php }?>
            </tr>
            </thead>
            <tbody>
            <?php if ($_smarty_tpl->tpl_vars['total']->value!=0){?>
            <?php  $_smarty_tpl->tpl_vars['exchange'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['exchange']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['exchange_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['exchange']->key => $_smarty_tpl->tpl_vars['exchange']->value){
$_smarty_tpl->tpl_vars['exchange']->_loop = true;
?>
            <?php if ($_smarty_tpl->tpl_vars['type']->value==0){?>
            <tr>
                <td><a href="?act=detail&type=<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
&account=<?php echo $_smarty_tpl->tpl_vars['exchange']->value['account'];?>
"><?php echo $_smarty_tpl->tpl_vars['exchange']->value['account'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['add_time_str'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['balance'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['integral'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['reward'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['integral_await'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['reward_await'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['operator'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['remark'];?>
</td>
            </tr>
            <?php }else{ ?>
            <tr>
                <td><a href="?act=detail&type=<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
&account=<?php echo $_smarty_tpl->tpl_vars['exchange']->value['business_account'];?>
"><?php echo $_smarty_tpl->tpl_vars['exchange']->value['business_account'];?>
</a></td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['add_time_str'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['balance'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['trade'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['operator'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['exchange']->value['remark'];?>
</td>
            </tr>
            <?php }?>
            <?php } ?>
            <?php }else{ ?>
            <?php if ($_smarty_tpl->tpl_vars['st']->value==''&&$_smarty_tpl->tpl_vars['et']->value==''){?>
            <tr>
                <td style="padding: 50px 0" colspan="7" align="center">未有交易产生！</td>
            </tr>
            <?php }else{ ?>
            <tr>
                <td style="padding: 50px 0" colspan="7" align="center">
                    <span class="text-size-24 blue icon-info-1" style="vertical-align: middle"></span>
                    <div class="text-size-18 tip-text inline-block">没有查询到符合条件的记录</div>
                </td>
            </tr>
            <?php }?>
            <?php }?>
            <tr>
                <td colspan="<?php if ($_smarty_tpl->tpl_vars['type']->value==0){?>9<?php }else{ ?>6<?php }?>">
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