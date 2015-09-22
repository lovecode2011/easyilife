<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:50
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/product/view.phtml" */ ?>
<?php /*%%SmartyHeaderCode:167890843455fa97feef2b15-43913959%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '517fe954e94831f8329c4fac546cccdb80294244' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/product/view.phtml',
      1 => 1441610256,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '167890843455fa97feef2b15-43913959',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'count' => 0,
    'keyword' => 0,
    'total' => 0,
    'product_list' => 0,
    'product' => 0,
    'totalPage' => 0,
    'go_first' => 0,
    'status' => 0,
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
  'unifunc' => 'content_55fa97ff17a359_97707776',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97ff17a359_97707776')) {function content_55fa97ff17a359_97707776($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left">产品管理</h5>
        <div class="pull-right">
            <a class="btn btn-primary" href="product.php">全部</a>&nbsp;
            <a class="btn btn-primary" href="product.php?status=1">待审核</a>&nbsp;
            <a class="btn btn-primary" href="product.php?status=2">已上架</a>&nbsp;
            <a class="btn btn-primary" href="product.php?status=3">已下架</a>&nbsp;
        </div>
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
                <div class="pull-right">
                    <label>搜索：</label>
                    <input class="text-input w150" type="text" id="small-input" name="keyword" value="<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
">&nbsp;
                    <button type="submit" class="btn btn-default">搜索</button>
                </div>
                </p>
            </fieldset>
            </form>
            <div class="clear"></div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>产品编号</th>
                <th>产品名称</th>
                <th>封面</th>
                <th>分类</th>
                <th>市场价</th>
                <th>售价</th>
                <th>状态</th>
                <th>排序</th>
                <th class="text-right">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($_smarty_tpl->tpl_vars['total']->value!=0){?>
            <?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
            <tr>
                <td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_sn'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</td>
                <td>
                    <?php if ($_smarty_tpl->tpl_vars['product']->value['img']!=''){?>
                    <img width="80" height="60" src="<?php echo $_smarty_tpl->tpl_vars['product']->value['img'];?>
" alt=""/>
                    <?php }else{ ?>
                    <img width="80" height="60" src="/upload/image/no-image.png" alt=""/>
                    <?php }?>
                </td>
                <td><?php echo $_smarty_tpl->tpl_vars['product']->value['category_name'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['product']->value['shop_price'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['product']->value['price'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['product']->value['status_str'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['product']->value['order_view'];?>
</td>
                <td class="text-right">
                    <?php if ($_smarty_tpl->tpl_vars['product']->value['status']==2){?>
                    <a href="?act=exam&sn=<?php echo $_smarty_tpl->tpl_vars['product']->value['product_sn'];?>
">审核</a>
                    <?php }?>
                </td>
            </tr>
            <?php } ?>
            <?php }else{ ?>
            <?php if ($_smarty_tpl->tpl_vars['keyword']->value==''){?>
            <tr>
                <td style="padding: 50px 0" colspan="7" align="center">暂无产品记录</td>
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
                <td colspan="9">
                    <div class="pull-right">
                        <div class="pages">
                            <span>共有<?php echo $_smarty_tpl->tpl_vars['total']->value;?>
条，<?php echo $_smarty_tpl->tpl_vars['totalPage']->value;?>
页，每页显示：<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
条</span>
                            <?php if ($_smarty_tpl->tpl_vars['go_first']->value){?>
                            <a href="?page=1&keyword=<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
<?php if ($_smarty_tpl->tpl_vars['status']->value){?>&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
<?php }?>">首页</a>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['has_prev']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['page']->value-1;?>
&keyword=<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
<?php if ($_smarty_tpl->tpl_vars['status']->value){?>&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
<?php }?>">上一页</a>
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
&keyword=<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
<?php if ($_smarty_tpl->tpl_vars['status']->value){?>&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
<?php }?>"><?php echo $_smarty_tpl->tpl_vars['pageNum']->value;?>
</a>
                            <?php }?>
                            <?php } ?>
                            <?php if ($_smarty_tpl->tpl_vars['has_many_next']->value){?>
                            ...
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['go_last']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['totalPage']->value;?>
&keyword=<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
<?php if ($_smarty_tpl->tpl_vars['status']->value){?>&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
<?php }?>">末页</a>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['has_next']->value){?>
                            <a href="?page=<?php echo $_smarty_tpl->tpl_vars['page']->value+1;?>
&keyword=<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
&count=<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
<?php if ($_smarty_tpl->tpl_vars['status']->value){?>&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
<?php }?>">下一页</a>
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
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>