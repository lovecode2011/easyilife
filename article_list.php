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

    $get_article_list = 'select `title`,`id`,`add_time`,`description`,`original` from '.$db->table('content');

    switch($mode)
    {
        case 'all':
            $get_article_list .= ' where `section_id` in (1,2,3)';
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

if($id <= 3)
{
    $get_section_list = 'select `section_name`,`id` from ' . $db->table('section') . ' limit 3';
    $section_list = $db->fetchAll($get_section_list);
    assign('section_list', $section_list);

    $get_article_list = 'select `title`,`id`,`add_time`,`description`,`original` from ' . $db->table('content') . ' where `section_id` in (1,2,3) order by `add_time` DESC';

    $article_list = $db->fetchAll($get_article_list);

    assign('article_list', $article_list);
    assign('title', '消息中心');
    assign('width', '25');
    assign('all', 'all');
} else {
    $get_section_list = 'select `section_name`, `id` from '.$db->table('section').' where `parent_id` = '.$id.' order by `id` asc';
    $section_list = $db->fetchAll($get_section_list);
    if( $section_list ) {
        $get_article_list = 'select `title`,`id`,`add_time`,`description`,`original` from ' . $db->table('content') . ' where `section_id`='.$section_list[0]['id'].' order by `add_time` DESC';
        $article_list = $db->fetchAll($get_article_list);

        assign('width', (100 / count($section_list)));
    } else {
        $get_article_list = 'select `title`,`id`,`add_time`,`description`,`original` from ' . $db->table('content') . ' where `section_id`='.$id.' order by `add_time` DESC';
        $article_list = $db->fetchAll($get_article_list);

        assign('width', '25');
    }
    assign('article_list', $article_list);
    assign('all', $id);
    assign('section_list', $section_list);

    $get_section = 'select `section_name` from '.$db->table('section').' where `id`='.$id;
    $section = $db->fetchRow($get_section);
    assign('title', $section['section_name']);
}

$smarty->display('news-list.phtml');