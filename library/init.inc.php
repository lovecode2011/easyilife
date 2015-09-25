<?php
/**
 * 初始化程序
 * @author winsen
 * @version 1.0.0
 * @date 2015-01-09
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
//设置系统相关参数
session_start();
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH', str_replace('library/init.inc.php', '',str_replace('\\', '/', __FILE__)));
if(!class_exists('AutoLoader'))
{
    include('AutoLoader.class.php');
}

$loader = AutoLoader::getInstance();
$configs = array('script_path'=>ROOT_PATH.'library/', 'class_path'=>ROOT_PATH.'library/');
$loader->setConfigs($configs);

$class_list = array('Smarty', 'Logs', 'MySQL', 'Code', 'JSSDK');
$loader->includeClass($class_list);
$script_list = array('configs','functions','lang', 'member', 'transaction', 'wechat');
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
$smarty->setCompileDir(ROOT_PATH.'data/compiles');
$smarty->setTemplateDir(ROOT_PATH.'themes/'.$config['themes']);
$smarty->setCacheDir(ROOT_PATH.'data/caches');
$smarty->setCacheLifetime(2);//设置缓存文件超时时间为1800秒

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

//测试数据
$_SESSION['account'] = 'SHQ000000';
$_SESSION['openid'] = '01234567890x';
if(!isset($_SESSION['openid']))
{
    $_SESSION['openid'] = '';
}

$code = getGET('code');
$state = getGET('state');
if($_SESSION['openid'] == '' && $code != '' && $state == 2048)
{
    $wechat_user = get_user_info($code, $config['appid'], $config['appsecret'], 'userinfo');

    if($wechat_user)
    {
        $log->record("get user openid:".$wechat_user->openid);
        $_SESSION['openid'] = $wechat_user->openid;

        $get_account = 'select `account` from '.$db->table('member').' where `openid`=\''.$wechat_user->openid.'\'';
        $account = $db->fetchOne($get_account);

        if(!$account)
        {
            $log->record("register new member");
            register_member($_SESSION['openid']);
        }

        $member_data = array(
            'sex' => $wechat_user->sex,
            'nickname' => $wechat_user->nickname,
            'province' => $wechat_user->province,
            'city' => $wechat_user->city,
            'headimg' => $wechat_user->headimgurl
        );

        $db->autoUpdate('member', $member_data, '`openid`=\''.$wechat_user->openid.'\'');
        $get_account = 'select `account` from '.$db->table('member').' where `openid`=\''.$wechat_user->openid.'\'';
        $_SESSION['account'] = $db->fetchOne($get_account);
    } else {
        echo 'ERROR 2048: 获取授权信息失败';
        exit;
    }
}

if($_SESSION['openid'] == '' || $_SESSION['account'] == '')
{
    echo '获取用户信息失败，请联系管理员';
    exit;
}

//微信JS调用参数
if(is_weixin())
{
    $jssdk = new JSSDK($config['appid'], $config['appsecret']);
    $signPackage = $jssdk->GetSignPackage();
    assign('signPackage', $signPackage);
}