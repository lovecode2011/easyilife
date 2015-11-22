<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-20
 * @version 
 */
include 'library/init.inc.php';

$operation = 'multi_delete';
$opera = check_action($operation, getPOST('opera'));

if( 'multi_delete' == $opera ) {
    $response = array('error'=>1, 'msg'=>'');

    $cid_list = getPOST('cid_list');
    $cid_list = json_decode($cid_list);
    if(!check_cross_domain() && isset($_SESSION['account']) && $_SESSION['account']) {
        if (!is_array($cid_list)) {
            $response['msg'] = '403:参数错误';
        }

        if ($response['msg'] == '') {
            foreach( $cid_list as $key => $cid ) {
                $cid_list[$key] = intval($cid);
            }
            $condition = implode(',', $cid_list);

            $get_product_sn = 'select product_sn from '.$db->table('product').' where `id` in ('.$condition.')';
            $product_sn_list = $db->fetchAll($get_product_sn);
            if( $product_sn_list ) {
                $multi_delete = 'delete from '.$db->table('distribution').' where `account`=\''.$_SESSION['account'].'\' and `product_sn` in (';
                foreach( $product_sn_list as $key => $value ) {
                    if( $key > 0 ) {
                        $multi_delete .= ',';
                    }
                    $multi_delete .= '\''.$value['product_sn'].'\'';
                }
                $multi_delete .= ')';
                if( $db->delete($multi_delete) ) {
                    $response['error'] = 0;
                }
            } else {
                $response['msg'] = '请选择要删除的分销产品';
            }
        }
    } else {
        if(empty($_SESSION['account']))
        {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }
    echo json_encode($response);
    exit;
}

$now = time();
$get_product_list = 'select p.`product_sn`,p.`id`,if(`promote_end`>'.$now.',`promote_price`,`price`) as `price`,p.`name`,p.`img` from '.$db->table('distribution').' as d join '.$db->table('product').' as p '.
    ' using(`product_sn`) where d.`account`=\''.$_SESSION['account'].'\' order by d.`add_time` DESC';

$product_list = $db->fetchAll($get_product_list);
assign('product_list', $product_list);

assign('title', '我的分销');
assign('mode', 'distribution');
$smarty->display('distribution.phtml');