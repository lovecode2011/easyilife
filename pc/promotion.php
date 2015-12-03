<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-12-3
 * @version 
 */
include 'library/init.inc.php';

$operation = 'paging';
$opera = check_action($operation, getPOST('opera'));

$id = 17;

$get_section = 'select `section_name`,`path` from '.$db->table('section').' where id = '.$id.' limit 1';
$section = $db->fetchRow($get_section);
assign('section', $section);
$get_section_list = 'select `id` from '.$db->table('section').' where `path` like \''.$section['path'].'%\'';
$section_list = $db->fetchAll($get_section_list);
$section_id_array = array();
if( $section_list ) {
    foreach( $section_list as $key => $section ) {
        array_push($section_id_array, $section['id']);
    }
}

$section_id_str = '('.implode(',', $section_id_array).')';
$get_article_list = 'select * from '.$db->table('content').' where `section_id` in '.$section_id_str;
$get_article_list .= ' and `status` <> 0 order by `order_view` asc, `last_modify` desc';
$page_count = 20;
assign('page_count', $page_count);

$article_list = $db->fetchAll($get_article_list);
$total = count($article_list);
$total_page = ceil($total / $page_count);
assign('total_page', $total_page);

if( 'paging' == $opera ) {
    $response = array(
        'error' => 1,
        'msg' => '',
    );
    if(!check_cross_domain() && isset($_SESSION['account']) ) {
        $page = intval(getPOST('page'));

        $page = $page > $total_page ? $total_page : $page;
        $page = $page < 1 ? 1 : $page;
        $offset = $page_count * ( $page - 1 );

        $get_article_list .= ' limit '.$offset.','.$page_count;
        $article_list = $db->fetchAll($get_article_list);

        assign('article_list', $article_list);
        assign('page', $page);
        $response['content'] = $smarty->fetch('promotion-item.phtml');
        $response['sql'] = $get_article_list;
        $response['error'] = 0;
    } else {
        if (empty($_SESSION['account'])) {
            $response['msg'] = '请先登录';
            $response['error'] = 2;
        } else {
            $response['msg'] = '404:参数错误';
        }
    }
    echo json_encode($response);
    exit;
}

assign('article_list', $article_list);

$smarty->display('promotion.phtml');
