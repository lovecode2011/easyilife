<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:39
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/category/add.phtml" */ ?>
<?php /*%%SmartyHeaderCode:144726150955fa97f311fed7-03500641%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '58168fb9ee06e1480edfbbd8dfc0a0a9fbbf5e1d' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/category/add.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '144726150955fa97f311fed7-03500641',
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
  'unifunc' => 'content_55fa97f31bf447_67984732',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97f31bf447_67984732')) {function content_55fa97f31bf447_67984732($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<style>
    
    .select-image {
        cursor: pointer;
    }
    
</style>
<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left">添加主营分类</h5>
        <div class="clear"></div>
    </div>
    <div class="basicInfo-main">
        <form id="categoryAddForm" method="post" action="">
            <fieldset>
                <p>
                    <label class="l-title">分类名：</label>
                    <input class="text-input w300" type="text" maxlength="64" id="category_name" name="category_name" value="" placeholder="请输入分类名"/>
                </p>
                <p>
                    <label class="l-title">父级分类：</label>
                    <select name="parent_id" class="w150">
                        <option value="0">顶级分类</option>
                        <?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['category_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value){
$_smarty_tpl->tpl_vars['category']->_loop = true;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['category']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['category']->value['name'];?>
</option>
                        <?php } ?>
                    </select>
                </p>
                <p>
                    <label class="l-title">图片：</label>
                    <input type="hidden" name="img" id="img" placeholder="">
                    <img class="select-image" alt="" src="/upload/image/no-image.png">&nbsp;
                </p>
                <p>
                    <label class="l-title"></label>
                    <button class="btn btn-primary" type="submit">保存</button>
                    <a class="btn btn-primary" href="javascript:history.go(-1);">返回</a>
                    <input type="hidden" name="opera" value="add" />
                </p>

            </fieldset>
        </form>
    </div>
</div>
<!-- END content -->
<!-- kindeditor编辑器 -->
<link rel="stylesheet" type="text/css" href="../plugins/kindeditor/plugins/code/prettify.css"/>
<link rel="stylesheet" type="text/css" href="../plugins/kindeditor/themes/default/default.css"/>
<script type="text/javascript" src="../plugins/kindeditor/kindeditor-all-min.js"></script>
<script type="text/javascript">
    
    KindEditor.ready(function(K) {
        var editor = K.editor({
            allowFileManager: true,
            uploadJson: '../plugins/kindeditor/platform_upload_json.php',
            fileManagerJson: '../plugins/kindeditor/platform_file_manager_json.php'
        });
        K('.select-image').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    imageUrl : '',
                    clickFn : function(url, title, width, height, border, align) {
                        if( !width ) {
                            width = 200 + 'px';
                        }
                        if( !height ) {
                            height = 150 + 'px';
                        }

                        K('#img').val(url);
                        K('.select-image').attr('src', url);
                        K('.select-image').css('display', 'block');
                        K('.select-image').attr('width', width);
                        K('.select-image').attr('height', height);
                        editor.hideDialog();
                    }
                });
            });
        });
    });
    
</script>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>