<?php
/**
 * 管理后台初始化程序
 * @author 王仁欢
 * @version 1.0.0
 * @date 2015-01-09
 */

//设置系统相关参数
session_start();
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH', str_replace('pc/library/init.inc.php', '',str_replace('\\', '/', __FILE__)));
define('BASE_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT_PATH));

if(!class_exists('AutoLoader'))
{
    include(ROOT_PATH.'library/AutoLoader.class.php');
}

$loader = AutoLoader::getInstance();
$configs = array('script_path'=>ROOT_PATH.'library/', 'class_path'=>ROOT_PATH.'library/');
$loader->setConfigs($configs);

$class_list = array('Smarty', 'Logs', 'MySQL', 'Code');
$loader->includeClass($class_list);
$script_list = array('configs','functions','lang', 'business', 'transaction', 'member');
$loader->includeScript($script_list);
//初始化数据库链接
global $db;
$db = new MySQL(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DBNAME, DB_PREFIX);

$debug_mode = true;//开启此项将关闭Smarty缓存模式，并开启日志跟踪
//初始化日志对象
global $log;
$log_file = date('Ymd').'.log';
$log = new Logs($debug_mode, $log_file);
//读取网站设置
$get_sysconf = 'select `key`,`value` from '.$db->table('sysconf');
global $config;
$config_tmp = $db->fetchAll($get_sysconf);
foreach($config_tmp as $tmp)
{
    $config[$tmp['key']] = $tmp['value'];
}

//初始化smarty对象
global $smarty;
$smarty = new Smarty();
$smarty->setCompileDir(ROOT_PATH.'pc/data/compiles');
$smarty->setTemplateDir(ROOT_PATH.'pc/themes/'.$config['themes']);
$smarty->setCacheDir(ROOT_PATH.'pc/data/caches');
$smarty->setCacheLifetime(1800);//设置缓存文件超时时间为1800秒

//Debug模式下每次都强制编译输出
if($debug_mode)
{
    $smarty->clearAllCache();
    $smarty->clearCompiledTemplate();
    $smarty->force_compile = true;
}

//设置系统设置
assign('config', $config);
//设置语言包
assign('LANG', $lang);
//设置网站参数
assign('config', $config);
//设置模板路径
assign('template_dir', 'themes/'.$config['themes'].'/');

//获取顶部导航栏
$get_top_nav = 'select * from '.$db->table('nav').' where `position`=\'top\' order by `order_view`';
$top_nav = $db->fetchAll($get_top_nav);
assign('top_nav', $top_nav);

$get_category_list = 'select * from '.$db->table('category').' where `business_account`=\'\' and `parent_id`=0 limit 8';
$category_list = $db->fetchAll($get_category_list);

foreach($category_list as $key=>$cat)
{
    $get_children = 'select `id`,`name`,`parent_id` from '.$db->table('category').' where `business_account`=\'\' and `parent_id`='.$cat['id'];
    $children = $db->fetchAll($get_children);

    if($children)
    {
        $category_list[$key]['children'] = $children;
    } else {
        $category_list[$key]['children'] = null;
    }
}

assign('category_list', $category_list);

if( isset($_SESSION['account']) && $_SESSION['account'] ) {
    assign('is_login', true);
    assign('account', $_SESSION['account']);

    $get_cart_count = 'select sum(number) from '.$db->table('cart').' where account = \''.$_SESSION['account'].'\' and checked = 1';
    $cart_count = $db->fetchOne($get_cart_count);
    assign('cart_count', $cart_count);
}