<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:34:15
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/member/network.phtml" */ ?>
<?php /*%%SmartyHeaderCode:63859482855fa9727508e16-95623759%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4ad937566413cb4ff9dab7b537d36eb17b75f587' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/member/network.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '63859482855fa9727508e16-95623759',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'data' => 0,
    'account' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa97275a1621_10065189',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97275a1621_10065189')) {function content_55fa97275a1621_10065189($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link rel="stylesheet" href="css/zTreeStyle.css" type="text/css">
<script type="text/javascript">
    var zNodes = <?php echo $_smarty_tpl->tpl_vars['data']->value;?>
;
</script>
<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left">会员网络图</h5>
        <!--        <div class="pull-right"><a class="btn btn-primary" href="?act=add">添加内容</a>&nbsp;<a class="btn btn-primary" href="?act=cycle">回收站</a></div>-->
        <div class="clear"></div>
    </div>
    <div class="content_wrap">
        <div class="zTreeDemoBackground left">
            <ul id="treeDemo" class="ztree"></ul>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/jquery.ztree.core-3.5.min.js"></script>
<script type="text/javascript">
    var account = "<?php echo $_smarty_tpl->tpl_vars['account']->value;?>
";
    var zNodes = <?php echo $_smarty_tpl->tpl_vars['data']->value;?>
;
    
    var setting = {
        async: {
            enable: true,
            url:"ajax.php",
            autoParam:["account"],
            otherParam:{"current":account,"opera":"get_children"},
            dataFilter: filter
        }
    };

    function filter(treeId, parentNode, responseData) {
        console.log(treeId);
        console.log(responseData.data);
        if ( responseData.error ) {
            alert(responseData.message);
            return null;
        }
        return responseData.data;
    }

    $(document).ready(function(){
        $.fn.zTree.init($("#treeDemo"), setting, zNodes);
    });
    
</script>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>