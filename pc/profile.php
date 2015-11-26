<?php
/**
 *
 * @author 王仁欢
 * @email wrh4285@163.com
 * @date 15-11-20
 * @version 
 */

include 'library/init.inc.php';

$operation = 'edit|avatar';
$action = 'view|avatar';
$template = 'profile.phtml';

$act = check_action($action, getGET('act'));
$opera = check_action($operation, getPOST('opera'));

$act = $act == '' ? 'view' : $act;

if( 'edit' == $opera ) {
    $response = array('error'=>1, 'msg'=>'');

    if( !check_cross_domain() && isset($_SESSION['account']) ) {
        $email = getPOST('email');
        $sex = getPOST('sex');
        $mobile = getPOST('mobile');

        if (!is_mobile($mobile)) {
            $response['msg'] .= '-手机号码格式不正确<br/>';
        } else {
            $mobile = $db->escape($mobile);
            //检查号码是否已被使用
            $check_mobile = 'select `account` from ' . $db->table('member') . ' where `mobile`=\'' . $mobile . '\' and `account`<>\'' . $_SESSION['account'] . '\'';

            if ($db->fetchOne($check_mobile)) {
                $response['msg'] = '-该号码已被其他用户使用<br/>';
            }
        }

        if ($email == '') {
            $response['msg'] .= '-请填写邮箱地址<br/>';
        } else {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = $db->escape($email);
            } else {
                $response['msg'] .= '-邮箱格式不正确<br/>';
            }
        }

        $sex_list = 'N|F|M';
        $sex = check_action($sex_list, $sex);

        if ($sex == '') {
            $sex = 'N';
        }

        if ($response['msg'] == '') {
            $member_data = array(
                'email' => $email,
                'sex' => $sex,
                'mobile' => $mobile
            );

            if ($db->autoUpdate('member', $member_data, '`account`=\'' . $_SESSION['account'] . '\'')) {
                $response['error'] = 0;
                $response['msg'] = '修改信息成功';
            } else {
                $response['msg'] = '系统繁忙，请稍后再试';
            }
        }
    } else {
        if( empty($_SESSION['account']) ) {
            $response['msg'] = '请先登陆';
        } else {
            $response['msg'] = '请从本站提交';
        }

    }

    echo json_encode($response);
    exit;
}

if( 'avatar' == $opera ) {
    $response = array('error'=>1, 'msg'=>'');

    if( $_SESSION['account'] ) {
        $headimg = $_FILES['headimg'];

        $get_user_info = 'select * from '.$db->table('member').' where account = \''.$_SESSION['account'].'\'';
        $user_info = $db->fetchRow($get_user_info);

        if ($headimg == '' || $headimg['error'] > 0) {
            $response['msg'] .= '-请上传头像<br/>';
        } else {
            $save_path = '../upload/image/headimg/';
            $file_name = '';
            $file_name_arr = explode('.', $headimg['name']);
            $tail = $file_name_arr[count($file_name_arr) - 1];

            do {
                $file_name = date('YmdHis_') . rand(10000, 99999) . '.' . $tail;
            } while (file_exists($save_path . $file_name));

            if (move_uploaded_file($headimg['tmp_name'], $save_path . $file_name)) {
                $php_url = dirname($_SERVER['PHP_SELF']) . '/';
                $php_url = '';
                $root_url = $php_url . 'upload/image/headimg/';
                $headimg = $root_url . $file_name;
            } else {
                $response['msg'] .= '-头像上传失败，请稍后再试<br/>';
            }
        }
        if ($response['msg'] == '') {
            $data = array(
                'headimg' => $headimg,
            );

            if ($db->autoUpdate('member', $data, 'account = \'' . $_SESSION['account'] . '\'')) {
                $response['error'] = 0;
                $response['msg'] = '头像已更新';

                if( $user_info['headimg'] && file_exists('../'.$user_info['headimg']) ) {
                    unlink('../'.$user_info['headimg']);
                }

            } else {
                $response['msg'] = '001:系统繁忙，请稍后再试';
            }
        }
    } else {
        $response['error'] = 2;
        $response['msg'] = '请先登陆';
    }
    assign('response', json_encode($response));
    $template = 'avatar.phtml';

}

if( 'avatar' == $act ) {

    $get_user_info = 'select * from '.$db->table('member').' where account = \''.$_SESSION['account'].'\'';
    $user_info = $db->fetchRow($get_user_info);
//    var_dump($user_info);exit;
    assign('user_info', $user_info);
    $template = 'avatar.phtml';
}

if( 'view' == $act ) {

    $level = array(
        0 => '普通会员',
        1 => '合伙人',
        2 => '高级合伙人'
    );
    assign('level', $level);

    $get_user_info = 'select * from '.$db->table('member').' where account = \''.$_SESSION['account'].'\'';
    $user_info = $db->fetchRow($get_user_info);

    assign('user_info', $user_info);
}

$smarty->display($template);

