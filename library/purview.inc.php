<?php
/**
 * 权限配置
 * @author 王仁欢
 * @date 2015-08-05
 * @version 1.0.0
 */
global $purview;
$purview = array(
    'pur_sysconf' => array(
        'pur_sysconf_add',
        'pur_sysconf_view',
        'pur_sysconf_edit',
        'pur_sysconf_del',
    ),
    'pur_member' => array(
        'pur_member_network',
        'pur_member_view',
        'pur_member_edit',
        'pur_member_del',
    ),
    'pur_recharge' => array(
        'pur_recharge_view',
        'pur_recharge_edit',
        'pur_recharge_del',
    ),
    'pur_withdraw' => array(
        'pur_withdraw_view',
        'pur_withdraw_edit',
        'pur_withdraw_del',
    ),
    'pur_account' => array(
        'pur_account_view',
    ),
    'pur_admin' => array(
        'pur_admin_add',
        'pur_admin_view',
        'pur_admin_edit',
        'pur_admin_del',
    ),
    'pur_role' => array(
        'pur_role_add',
        'pur_role_view',
        'pur_role_edit',
        'pur_role_del',
    ),
    'pur_self' => array(
        'pur_passwd_edit',
    ),
    'pur_order' => array(
        'pur_order_view',
        'pur_order_del',
        'pur_order_edit',
    ),
    //模板控制
    /*
    'pur_template' => array(
        'pur_template_view',
        'pur_template_apply',
    ),
    */
    //产品
    'pur_product' => array(
        'pur_product_view',
        'pur_product_add',
        'pur_product_edit',
        'pur_product_del',
    ),

    //产品分类
    'pur_category' => array(
        'pur_category_view',
        'pur_category_add',
        'pur_category_edit',
        'pur_category_del',
    ),

);

global $L_purview;
$L_purview = array(
    'pur_sysconf' => '系统设置',
    'pur_sysconf_add'=>'添加系统参数',
    'pur_sysconf_view'=>'查看系统参数',
    'pur_sysconf_edit'=>'修改系统参数',
    'pur_sysconf_del'=>'删除系统参数',

    'pur_member' => '会员管理',
    'pur_member_network'=>'会员网络图',
    'pur_member_view'=>'会员查看',
    'pur_member_edit'=>'编辑会员',
    'pur_member_del'=>'删除会员',

    'pur_adpos' => '广告位置',
    'pur_adpos_add'=>'添加广告位置',
    'pur_adpos_view'=>'查看广告位置',
    'pur_adpos_edit'=>'编辑广告位置',
    'pur_adpos_del'=>'删除广告位置',

    'pur_ad' => '广告',
    'pur_ad_add'=>'添加广告',
    'pur_ad_view'=>'查看广告列表',
    'pur_ad_edit'=>'编辑广告',
    'pur_ad_del'=>'删除广告',

    'pur_recharge' => '充值管理',
    'pur_recharge_view'=>'查看充值记录',
    'pur_recharge_edit'=>'修改充值状态',
    'pur_recharge_del'=>'删除充值记录',

    'pur_withdraw' => '提现管理',
    'pur_withdraw_view'=>'查看提现记录',
    'pur_withdraw_edit'=>'编辑提现状态',
    'pur_withdraw_del'=>'删除提现记录',

    'pur_account' => '账户明细',
    'pur_account_view' => '查看账户明细',

    'pur_admin' => '管理员',
    'pur_admin_add'=>'添加管理员',
    'pur_admin_view'=>'查看管理员',
    'pur_admin_edit'=>'编辑管理员',
    'pur_admin_del'=>'删除管理员',

    'pur_role' => '管理员角色',
    'pur_role_add'=>'添加管理员角色',
    'pur_role_view'=>'查看管理员角色',
    'pur_role_edit'=>'编辑管理员角色',
    'pur_role_del'=>'删除管理员角色',

    'pur_product' => '产品管理',
    'pur_product_add'=>'添加产品',
    'pur_product_view'=>'查看产品',
    'pur_product_edit'=>'编辑产品',
    'pur_product_del'=>'删除产品',

    'pur_self' => '个人信息',
    'pur_passwd_edit' => '密码修改',

    'pur_order' => '订单管理',
    'pur_order_view' => '查看订单',
    'pur_order_edit' => '编辑订单状态',
    'pur_order_del' => '删除订单',

    'pur_template' => '主题',
    'pur_template_view' => '模板列表',
    'pur_template_apply' => '更换模板',

    'pur_stastics' => '站点统计',
    'pur_stastics_view' => '查看统计',
);

global $menus;
$menus = array(
    'pur_product' => array('url'=>'product.php', 'title'=>'产品管理', 'parent' => 'menu_product'),
    'pur_member' => array('url'=>'member.php', 'title'=>'会员管理', 'parent' => 'menu_member'),
    'pur_account' => array('url'=>'account.php', 'title'=>'账户明细', 'parent' => 'menu_account'),
    'pur_withdraw' => array('url'=>'withdraw.php', 'title'=>'提现管理', 'parent' => 'menu_account'),
    'pur_recharge' => array('url'=>'recharge.php', 'title'=>'充值管理', 'parent' => 'menu_account'),
    'pur_admin' => array('url'=>'admin.php', 'title'=>'管理员管理', 'parent' => 'menu_admin'),
    'pur_role' => array('url'=>'role.php', 'title'=>'管理员角色管理', 'parent' => 'menu_admin'),
    'pur_sysconf' => array('url'=>'sysconf.php', 'title'=>'参数设置', 'parent' => 'menu_site'),
    'pur_passwd' => array('url' => 'password.php', 'title' => '修改密码', 'parent' => 'memu_self'),
    'pur_adpos' => array('url' => 'adpos.php', 'title' => '广告位置管理', 'parent' => 'menu_site'),
    'pur_ad' => array('url' => 'ad.php', 'title' => '广告管理', 'parent' => 'menu_site'),
    'pur_stastics' => array('url' => 'stastics.php', 'title' => '查看统计', 'parent' => 'menu_stastics')
);

global $topMenus;
$topMenus = array(
    'menu_site' => array('title' => '系统设置', 'icon' => '&#xe607;'),
    'menu_member' => array('title' => '会员管理', 'icon' => '&#xe603;'),
    'menu_account' => array('title' => '财务管理', 'icon' => '&#xe603;'),
    'menu_product' => array('title' => '产品管理', 'icon' => '&#xe603;'),
    'menu_order' => array('title' => '订单管理', 'icon' => '&#xe603;'),
    'menu_admin' => array('title' => '权限管理', 'icon' => '&#xe601;'),
    'menu_self' => array('title' => '个人信息', 'icon' => '&#xe602;'),
    'menu_stastics' => array('title' => '站点统计', 'icon' => '&#xe604;'),
);
