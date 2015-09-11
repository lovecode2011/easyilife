<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 2015-09-02
 * @version 1.0.0
 */

include 'library/init.inc.php';

if( !isset($_SESSION['account']) ) {
    echo json_decode(array(
        'error' => 1,
        'message' => '请先登陆',
    ));
    exit;
}

if( check_cross_domain() ) {
    echo json_decode(array(
        'error' => 1,
        'message' => '请从本站提交数据',
    ));
    exit;
}

$operation = 'get_children';
$opera = check_action($operation, getPOST('opera'));

if( 'get_children' == $opera ) {

    $account = trim(getPOST('account'));
    if( '' == $account ) {
        echo json_encode('');
    }
}
