<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:28
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/brand/add.phtml" */ ?>
<?php /*%%SmartyHeaderCode:170055030955fa97e88ba973-13268157%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '39bf16315791ac68261ffb8f0bd290d114c0d0e1' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/brand/add.phtml',
      1 => 1442449503,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '170055030955fa97e88ba973-13268157',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa97e8997f10_62260040',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa97e8997f10_62260040')) {function content_55fa97e8997f10_62260040($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
        <h5 class="pull-left">添加品牌</h5>
        <div class="clear"></div>
    </div>
    <div class="adminUser-main">
        <form id="brandAddForm" name="brandAddForm" method="post">
            <fieldset>
                <p>
                    <label class="l-title">名称：</label>
                    <input class="text-input w200" type="text" id="name" name="name" placeholder="请输入品牌名称">
                </p>
                <p>
                    <label class="l-title">图片：</label>
                    <input type="hidden" name="img" id="img" placeholder="">
                    <img class="select-image" alt="" src="/upload/image/no-image.png">&nbsp;
                </p>
                <p>
                    <label class="l-title">品牌简介：</label>
                    <textarea name="desc" rows="8" cols="80"></textarea>
                </p>
                <p>
                    <label class="l-title"></label>
                    <input type="hidden" name="opera" value="add" />
                    <button class="btn btn-primary" type="submit">添加</button> &nbsp;<a href="javascript:history.go(-1);" class="btn btn-primary">返回</a>
                </p>

            </fieldset>
        </form>
    </div>
</div>
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
<!-- END content -->
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>