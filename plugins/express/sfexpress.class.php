<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/21
 * Time: 上午11:07
 */
global $plugins;

$i = isset($plugins) ? count($plugins) : 0;
$plugins[$i]['name'] = '顺丰物流';//插件名称
$plugins[$i]['desc'] = '顺丰快递';//插件描述
$plugins[$i]['self_delivery'] = 0;//不支持自提
$plugins[$i]['plugins'] = 'sfexpress.class.php';