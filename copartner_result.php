<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-4
 * @version 
 */

include 'library/init.inc.php';

//是否已是合伙人
$get_level_id = 'select `level_id` from '.$db->table('member').' where account = \''.$_SESSION['account'].'\' limit 1';
$level_id = $db->fetchOne($get_level_id);

assign('level', $level_id);
$smarty->display('copartner.phtml');