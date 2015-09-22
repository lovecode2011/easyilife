<?php /* Smarty version Smarty-3.1.13, created on 2015-09-17 18:37:52
         compiled from "/Library/WebServer/Documents/easyilife/control/themes/sysconf/view.phtml" */ ?>
<?php /*%%SmartyHeaderCode:43765409155fa9800b15a76-63486910%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b10a8ea3f8dd22f68110ea8b3c3d0d4c829d2c28' => 
    array (
      0 => '/Library/WebServer/Documents/easyilife/control/themes/sysconf/view.phtml',
      1 => 1440508153,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '43765409155fa9800b15a76-63486910',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'subTitle' => 0,
    'sysconf' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_55fa9800c1c579_96061084',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55fa9800c1c579_96061084')) {function content_55fa9800c1c579_96061084($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ("library/navbar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("library/sidebar.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link rel="stylesheet" type="text/css" href="../plugins/kindeditor/plugins/code/prettify.css"/>
<link rel="stylesheet" type="text/css" href="../plugins/kindeditor/themes/default/default.css"/>
<script type="text/javascript" src="../plugins/kindeditor/kindeditor-all-min.js"></script>
<!-- content -->
<div id="content">
    <div class="content-title">
        <h5 class="pull-left"><?php echo $_smarty_tpl->tpl_vars['subTitle']->value;?>
</h5>
        <div class="clear"></div>
    </div>
    <div class="sysconf-main">
        <table class="table">
            <thead>
            <tr>
                <th>系统参数</th>
                <th>值</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php  $_smarty_tpl->tpl_vars['conf'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['conf']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sysconf']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['conf']->key => $_smarty_tpl->tpl_vars['conf']->value){
$_smarty_tpl->tpl_vars['conf']->_loop = true;
?>
            <form id="sysconfForm" name="sysconfForm" method="post">
                <tr>
                    <td><?php echo $_smarty_tpl->tpl_vars['conf']->value['name'];?>
</td>
                    <?php if ($_smarty_tpl->tpl_vars['conf']->value['type']=='text'){?>
                    <td>
                        <input class="text-input w300" type="<?php echo $_smarty_tpl->tpl_vars['conf']->value['type'];?>
" id="siteName" name="value" value="<?php echo $_smarty_tpl->tpl_vars['conf']->value['value'];?>
">
                    </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['conf']->value['type']=='img'){?>
                    <td>
                        <input class="text-input" style="cursor: pointer;" type="button" id="select-image-<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
" value="请选择图片"/>
                        <input type="hidden" name="value" id="<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['conf']->value['value'];?>
" />
                        <br /><img <?php if ($_smarty_tpl->tpl_vars['conf']->value['value']==''){?>style="display: none"<?php }?> id="show-image-<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
" src="<?php echo $_smarty_tpl->tpl_vars['conf']->value['value'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['conf']->value['name'];?>
" />
                        <script type="text/javascript">
                            KindEditor.ready(function(K)
                            {
                                var editor = K.editor({
                                    allowFileManager : true,
                                    uploadJson : '../plugins/kindeditor/upload_json.php',
                                    fileManagerJson : '../plugins/kindeditor/file_manager_json.php'
                                });
                                K('#select-image-<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
').click(function() {
                                    editor.loadPlugin('image', function() {
                                        editor.plugin.imageDialog({
                                            imageUrl : '',
                                            clickFn : function(url, title, width, height, border, align) {
                                                if( !width ) {
                                                    width = 187 + 'px';
                                                }
                                                if( !height ) {
                                                    height = 140 + 'px';
                                                }

                                                K('#<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
').val(url);
                                                K('#show-image-<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
').attr('src', url);
                                                K('#show-image-<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
').css('display', 'block');
                                                editor.hideDialog();
                                            }
                                        });
                                    });
                                });
//    prettyPrint();
                            });
                        </script>
                    </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['conf']->value['type']=='radio'){?>
                    <td class="clear">
                        <input type="radio" name="value" value="1" <?php if ($_smarty_tpl->tpl_vars['conf']->value['value']==1){?>checked="checked"<?php }?>> <em>是</em> &nbsp;
                        <input type="radio" name="value" value="0" <?php if ($_smarty_tpl->tpl_vars['conf']->value['value']==0){?>checked="checked"<?php }?>> <em>否</em>
                    </td>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['conf']->value['type']=='textarea'){?>
                    <td>
                        <textarea rows="5" class="rn w-60" name="value"><?php echo $_smarty_tpl->tpl_vars['conf']->value['value'];?>
</textarea>
                    </td>
                    <?php }?>
                    <td><?php echo $_smarty_tpl->tpl_vars['conf']->value['remark'];?>
</td>
                    <td><button class="btn btn-primary" type="submit">保存</button></td>
                    <input type="hidden" name="opera" value="edit" />
                    <input type="hidden" name="key" value="<?php echo $_smarty_tpl->tpl_vars['conf']->value['key'];?>
" />
                    <input type="hidden" name="type" value="<?php echo $_smarty_tpl->tpl_vars['conf']->value['type'];?>
" />
                </tr>
            </form>
            <?php } ?>
            <!--                <tr>-->
            <!--                    <td  colspan="3"><button class="btn btn-primary" type="submit">保存设置</button></td>-->
            <!--                </tr>-->
            </tbody>
        </table>

    </div>
</div>
<!-- END content -->

<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html><?php }} ?>