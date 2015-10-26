<?php
/**
 * 初始化数据
 */
include 'library/init.inc.php';
$loader->includeScript('purview');

header('Content-Type: text/html;charset=utf-8');
$table = array();
$data = array();

//站点参数
$table[] = 'sysconf';
$data[] = array(
    array('key'=>'recommend_integral', 'name'=>'推广赠送积分', 'value'=>10, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'level_1', 'name'=>'一级分销比例', 'value'=>0.3, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'level_2', 'name'=>'二级分销比例', 'value'=>0.2, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'level_3', 'name'=>'三级分销比例', 'value'=>0.1, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'access_token', 'name'=>'accessToken', 'value'=>'', 'type'=>'text', 'group'=>'wechat', 'remark'=>''),
    array('key'=>'appid', 'name'=>'appid', 'value'=>'', 'type'=>'text', 'group'=>'wechat', 'remark'=>''),
    array('key'=>'appsecret', 'name'=>'appsecret', 'value'=>'', 'type'=>'text', 'group'=>'wechat', 'remark'=>''),
    array('key'=>'category_depth', 'name'=>'产品分类深度', 'value'=>4, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'domain', 'name'=>'域名', 'value'=>'localhost/sbx', 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'expired', 'name'=>'接口凭证超时时间', 'value'=>0, 'type'=>'text', 'group'=>'wechat', 'remark'=>''),
    array('key'=>'fee_rate', 'name'=>'提现手续费比例', 'value'=>0.1, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'integral_rate', 'name'=>'积分比值', 'value'=>100, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'public_account', 'name'=>'公众号原始ID', 'value'=>'', 'type'=>'text', 'group'=>'wechat', 'remark'=>''),
    array('key'=>'reward_rate', 'name'=>'佣金比例', 'value'=>1, 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'site_name', 'name'=>'站点名称', 'value'=>'', 'type'=>'text', 'group'=>'config', 'remark'=>''),
    array('key'=>'themes', 'name'=>'模板文件', 'value'=>'sbx', 'type'=>'text', 'group'=>'themes', 'remark'=>''),
    array('key'=>'token', 'name'=>'token', 'value'=>'', 'type'=>'text', 'group'=>'wechat', 'remark'=>''),
    array('key'=>'withdraw_min', 'name'=>'最小提现金额', 'value'=>100, 'type'=>'text', 'group'=>'config', 'remark'=>'')
);
/*
//管理员
$table[] = 'admin';
$data[] = array(
);

//角色
$table[] = 'role';
$data[] = array(
);
 */

/*
//产品分类
$table[] = 'category';
$data[] = array(
);

$table[] = 'product';
$data[] = array(
);
 */
$table[] = 'card_pool';
$data[] = array(
    array('account'=>'SJ000000', 'status'=>1)
);

$table[] = 'payment';
$data[] = array(
    array('name'=>'微信支付', 'plugins'=>'wechat', 'desc'=>'微信支付', 'status'=>1)
);

echo '初始化数据:<br/>';
foreach($table as $key=>$name)
{
    $db->query('truncate table '.$db->table($name).';');
    echo $name;

    $dot_count = 30 - strlen($name);

    while($dot_count--)
    {
        echo '-';
    }

    if($db->autoInsert($name, $data[$key]))
    {
        echo ' <font color="green">success</font><br/>';
    } else {
        echo ' <font color="red">fail</font><br/>'.$db->errmsg();
    }
}
