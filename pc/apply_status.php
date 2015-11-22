<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-22
 * @version 
 */

include 'library/init.inc.php';

if(!isset($_SESSION['business_account']))
{
    redirect('index.php');
}

$get_business_status = 'select `status` from '.$db->table('business').' where `business_account`=\''.$_SESSION['business_account'].'\'';
assign('status', $db->fetchOne($get_business_status));

$smarty->display('apply_status.phtml');