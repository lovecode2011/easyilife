<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/10/26
 * Time: 上午10:44
 */
include 'library/init.inc.php';
back_base_init();

$template = 'wechat_rule/';
assign('subTitle', '微信回复规则管理');

$action = 'view|add|edit|delete';
$operation = 'add|edit|remove';

$act = check_action($action, getGET('act'));
$act = ( $act == '' ) ? 'view' : $act;

$opera = check_action($operation, getPOST('opera'));

if('edit' == $opera)
{
    $response = array('error'=>1, 'msg'=>'', 'errmsg'=>array());

    $eid = intval(getPOST('eid'));
    $rule = trim(getPOST('rule'));
    $response_content = trim(getPOST('response_content'));
    $name = trim(getPOST('name'));
    $order_view = intval(getPOST('order_view'));
    $enabled = intval(getPOST('enabled'));
    $match_mode = intval(getPOST('match_mode'));

    if($eid == '')
    {
        $response['msg'] = '参数错误';
    }

    if($rule == '')
    {
        $response['errmsg']['rule'] = '-请填写关键词';
    } else {
        $rule = $db->escape($rule);
    }

    if($response_content == '')
    {
        $response['errmsg']['rule'] = '-请填写回复内容';
    } else {
        $response_content = $db->escape($response_content);
    }

    if($name == '')
    {
        $response['errmsg']['name'] = '-请填写规则名称';
    } else {
        $name = $db->escape($name);
    }

    if($order_view <= 0)
    {
        $order_view = 50;
    }

    if(count($response['errmsg']) == 0 && $response['msg'] == '')
    {
        $db->begin();

        $response_data = array(
            'content' => $response_content
        );

        $get_response_id = 'select `response_id` from '.$db->table('wx_rule').' where `id`='.$eid;
        $response_id = $db->fetchOne($get_response_id);

        if($db->autoUpdate('wx_response', $response_data, '`id`='.$response_id))
        {
            $rule_data = array(
                'match_mode' => $match_mode,
                'enabled' => $enabled,
                'order_view' => $order_view,
                'rule' => $rule,
                'name' => $name
            );

            if($db->autoUpdate('wx_rule', $rule_data, '`id`='.$eid))
            {
                $db->commit();
                $response['error'] = 0;
                $response['msg'] = '修改规则成功';
            } else {
                $db->rollback();
                $response['msg'] = '修改规则失败，请稍后再试';
            }
        } else {
            $response['msg'] = '系统繁忙，请稍后再试';
            $db->rollback();
        }
    }

    echo json_encode($response);
    exit;
}

if('add' == $opera)
{
    $response = array('error'=>1, 'msg'=>'', 'errmsg'=>array());

    $rule = trim(getPOST('rule'));
    $response_content = trim(getPOST('response_content'));
    $name = trim(getPOST('name'));
    $order_view = intval(getPOST('order_view'));
    $enabled = intval(getPOST('enabled'));
    $match_mode = intval(getPOST('match_mode'));

    if($rule == '')
    {
        $response['errmsg']['rule'] = '-请填写关键词';
    } else {
        $rule = $db->escape($rule);
    }

    if($response_content == '')
    {
        $response['errmsg']['rule'] = '-请填写回复内容';
    } else {
        $response_content = $db->escape($response_content);
    }

    if($name == '')
    {
        $response['errmsg']['name'] = '-请填写规则名称';
    } else {
        $name = $db->escape($name);
    }

    if($order_view <= 0)
    {
        $order_view = 50;
    }

    if(count($response['errmsg']) == 0)
    {
        $db->begin();

        $response_data = array(
            'msgType'=>'text',
            'content' => $response_content
        );

        if($db->autoInsert('wx_response', array($response_data)))
        {
            $response_id = $db->get_last_id();

            $rule_data = array(
                'response_id' => $response_id,
                'match_mode' => $match_mode,
                'enabled' => $enabled,
                'order_view' => $order_view,
                'rule' => $rule,
                'name' => $name
            );

            if($db->autoInsert('wx_rule', array($rule_data)))
            {
                $db->commit();
                $response['error'] = 0;
                $response['msg'] = '新增规则成功';
            } else {
                $db->rollback();
                $response['msg'] = '新增规则失败，请稍后再试';
            }
        } else {
            $response['msg'] = '系统繁忙，请稍后再试';
            $db->rollback();
        }
    }

    echo json_encode($response);
    exit;
}

if('edit' == $act)
{
    $id = intval(getGET('id'));

    if($id <= 0)
    {
        show_system_message('参数错误');
    }

    $get_rule = 'select r.*,res.content from '.$db->table('wx_rule').' as r join '.$db->table('wx_response').' as res on r.response_id=res.id where r.`id`='.$id;
    $rule = $db->fetchRow($get_rule);

    assign('rule', $rule);
}

if('view' == $act)
{
    $where = '';

    $keyword = trim(getGET('keyword'));
    if( '' != $keyword ) {
        $keyword = $db->escape($keyword);
        $where .= ' and `name` like \'%'.$keyword.'%\'';
    }

    $count = intval(getGET('count'));
    $count_array = array(10, 25, 50 , 100);
    $count = ( in_array($count, $count_array) ) ? $count : 10;

    $get_total = 'select count(*) from '.$db->table('wx_rule').' as a where 1 ';
    $get_total .= $where;

    $total = $db->fetchOne($get_total);
    $total_page = ceil( $total / $count );

    $page = intval(getGET('page'));
    $page = ( $page > $total_page ) ? $total_page : $page;
    $page = ( 0 >= $page ) ? 1 : $page;

    create_pager($page, $total_page, $total);
    assign('count', $count);
    assign('keyword', $keyword);

    $offset = ($page - 1) * $count;

    $get_rule_list = 'select * from '.$db->table('wx_rule').' where 1 ';
    $get_rule_list .= $where;
    $get_rule_list .= ' order by order_view asc, id desc';
    $get_rule_list .= ' limit '.$offset.','.$count;
    $rule_list = $db->fetchAll($get_rule_list);

    assign('rule_list', $rule_list);
}

$template .= $act.'.phtml';
$smarty->display($template);