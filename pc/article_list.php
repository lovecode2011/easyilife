<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/10/10
 * Time: 上午10:06
 */
include 'library/init.inc.php';

$operation = 'sort';
$opera = check_action($operation, getPOST('opera'));

if('sort' == $opera)
{
    $response = array('error' => 1, 'msg' => '');

    $mode = getPOST('mode');

    if($mode != 'all')
    {
        $mode = intval($mode);
    }

    $get_article_list = 'select `title`,`id`,`add_time`,`description` from '.$db->table('content');

    switch($mode)
    {
        case 'all':
            break;
        default:
            $get_article_list .= ' where `section_id`='.$mode;
            break;
    }

    $get_article_list .= ' order by `add_time` DESC';
    $article_list = $db->fetchAll($get_article_list);
    assign('article_list', $article_list);

    $response['error'] = 0;
    $response['content'] = $smarty->fetch('news-list-item.phtml');

    echo json_encode($response);
    exit;
}

$id = getGET('id');
$id = intval($id);
$template = 'news-list.phtml';

$page = intval(getGET('page'));

if($id <= 4)
{
    $get_section_list = 'select `section_name`,`id` from ' . $db->table('section') . ' limit 3';
    $section_list = $db->fetchAll($get_section_list);
    assign('section_list', $section_list);

    $get_article_list = 'select `title`,`id`,`add_time`,`description` from ' . $db->table('content') . ' order by `add_time` DESC';
    $article_list = $db->fetchAll($get_article_list);

    assign('article_list', $article_list);
    assign('title', '消息中心');
} else {
    $get_section_list = 'select `section_name`, `id` from '.$db->table('section').' where `parent_id` = '.$id.' order by `id` asc';
    $section_list = $db->fetchAll($get_section_list);
    $section_id_array = array();
    array_push($section_id_array, $id);
    if( $section_list ) {
        foreach( $section_list as $key => $s ) {
            array_push($section_id_array, $s['id']);
        }
    }
    if( !empty($section_id_array) ) {
        $section_id_str = implode(',', $section_id_array);
    }

    $article_where = ' where `section_id` in ('.$section_id_str.')';

    $get_section = 'select `section_name`, `parent_id` from '.$db->table('section');
    $section = $db->fetchRow($get_section.' where id = '.$id);
    if( $id == 5 || $section['parent_id'] == 5 ) {
        $template = 'health.phtml';
        if( $section['parent_id'] == 5 ) {
            $get_section .= ' where parent_id = 5';
            $section = $db->fetchRow($get_section);
            $get_section_list = 'select `section_name`, `id` from '.$db->table('section').' where `parent_id` = 5 order by `id` asc';
            $section_list = $db->fetchAll($get_section_list);
            $sub_section_id_on = $id;
            $article_where = ' where `section_id` = '.$id;

        } else {
            $get_section_list = 'select `section_name`, `id` from '.$db->table('section').' where `parent_id` = '.$id.' order by `id` asc';
            $section_list = $db->fetchAll($get_section_list);
            $sub_section_id_on = $id;
        }
        $title = $section['section_name'];
        assign('sub_section_id_on', $sub_section_id_on);
    }
    $page_count = 10;
    $get_total = 'select count(*) from '.$db->table('content').$article_where;
    $total = $db->fetchOne($get_total);
    $totalPage = ceil($total/$page_count);
    $page = ($page >= $totalPage) ? $totalPage : $page;
    $page = (0 >= $page ) ? 1 : $page;
    create_pager($page, $totalPage, $total);

    $offset = ($page - 1) * $page_count;

    $get_article_list = 'select `title`,`id`,`add_time`,`description`, `original` from ' . $db->table('content') .$article_where. ' order by `add_time` DESC';
    $get_article_list .= ' limit '.$offset.','.$page_count;
    $article_list = $db->fetchAll($get_article_list);
    assign('article_list', $article_list);
    assign('title', $title);
    assign('section_list', $section_list);
    assign('id', $id);
}
$smarty->display($template);