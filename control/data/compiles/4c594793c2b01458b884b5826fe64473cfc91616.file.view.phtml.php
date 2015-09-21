<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:34:37
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/member/view.phtml" */ ?>
<?php /*%%SmartyHeaderCode:96447112655fa973d19f569-29777136%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4c594793c2b01458b884b5826fe64473cfc91616' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/member/view.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '96447112655fa973d19f569-29777136',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'subTitle' => 0,
    'count' => 0,
    'total' => 0,
    'member_list' => 0,
    'member' => 0,
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
  'unifunc' => 'content_55fa973d3103d1_86346693',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa973d3103d1_86346693')) {function content_55fa973d3103d1_86346693($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left"><?php echo $_smarty_tpl->tpl_vars['subTitle']->value;?>
</h5>
<!--        <div class="pull-right"><a class="btn btn-primary" href="?act=add">添加内容</a>&nbsp;<a class="btn btn-primary" href="?act=cycle">回收站</a></div>-->
        <div class="clear"></div>
    </div>
    <div class="article-main">
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
<!--                <div class="pull-right">-->
<!--                    <label>搜索：</label>-->
<!--                    <input class="text-input w150" type="text" id="small-input" name="keyword" value="">&nbsp;-->
<!--                    <button type="submit" class="btn btn-default">搜索</button>-->
<!--                </div>-->
                </p>
            </fieldset>
            </form>
            <div class="clear"></div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>昵称</th>
                <th>帐号</th>
                <th>性别</th>
                <th>手机</th>
                <th>邮箱</th>
                <th>是否关注</th>
                <th class="text-right">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($_smarty_tpl->tpl_vars['total']->value!=0){?>
            <?php  $_smarty_tpl->tpl_vars['member'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['member']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['member_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['member']->key => $_smarty_tpl->tpl_vars['member']->value){
$_smarty_tpl->tpl_vars['member']->_loop = true;
?>
            <tr>
                <td><?php echo $_smarty_tpl->tpl_vars['member']->value['id'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['member']->value['nickname'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['member']->value['account'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['member']->value['sex'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['member']->value['mobile'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['member']->value['email'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['member']->value['subscribed'];?>
</td>
                <td class="text-right">
                    <a href="?act=network&account=<?php echo $_smarty_tpl->tpl_vars['member']->value['account'];?>
">查看</a>
                    | <a href="?act=edit&account=<?php echo $_smarty_tpl->tpl_vars['member']->value['account'];?>
">编辑</a>
                    <?php if ($_smarty_tpl->tpl_vars['member']->value['status']==1){?>
                    | <a onclick="javascript:if(confirm('确认要拉黑？')) return true; else return false;" href="?act=delete&account=<?php echo $_smarty_tpl->tpl_vars['member']->value['account'];?>
">拉黑</a></td>
                    <?php }else{ ?>
                    | <a onclick="javascript:if(confirm('确认要撤销拉黑？')) return true; else return false;" href="?act=revoke&account=<?php echo $_smarty_tpl->tpl_vars['member']->value['account'];?>
">撤销</a></td>

                    <?php }?>
            </tr>
            <?php } ?>
            <?php }else{ ?>
            <tr>
                <td style="padding: 50px 0" colspan="8" align="center">您还没有会员</td>
            </tr>
            <?php }?>

            <tr>
                <td colspan="8">
                    <div class="pull-right">
                        <div class="pages">
                            <span>共有<?php echo $_smarty_tpl->tpl_vars['total']->value;?>
条，<?php echo $_smarty_tpl->tpl_vars['totalPage']->value;?>
页，每页显示：<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
条</span>
                            <?php if ($_smarty_tpl->tpl_vars['go_first']->value){?>
                            <a href="?page=1&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
">首页</a>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['has_prev']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['page']->value-1;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
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
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['pageNum']->value;?>
</a>
                            <?php }?>
                            <?php } ?>
                            <?php if ($_smarty_tpl->tpl_vars['has_many_next']->value){?>
                            ...
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['go_last']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['totalPage']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
">末页</a>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['has_next']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['page']->value+1;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
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
<script type="text/javascript">
    $(function(){
        $('select[name=count]').change(function(){
            window.location.href="member.php?count="+$(this).val();
        });
    });
</script>

<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>