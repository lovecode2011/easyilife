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
    array('key'=>'level_3', 'name'=>'三级分销比例', 'value'=>0.1, 'type'=>'text', 'group'=>'config', 'remark'=>'')
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

//产品分类
$table[] = 'category';
$data[] = array(
);

$table[] = 'product';
$data[] = array(
);

$table[] = 'card_pool';
$data[] = array(
);
*/

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
