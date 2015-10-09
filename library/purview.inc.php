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
    'pur_business' => array(
        'pur_business_view',
        'pur_business_edit',
        'pur_business_exam',
        'pur_business_frozen',
    ),
    'pur_exchange' => array(
        'pur_exchange_view',
        'pur_exchange_reward',
    ),
    'pur_withdraw' => array(
        'pur_withdraw_view',
        'pur_withdraw_edit',
        'pur_withdraw_del',
        'pur_withdraw_log',
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
        'pur_product_exam',
//        'pur_product_edit',
//        'pur_product_del',
    ),

    //主营分类
    'pur_category' => array(
        'pur_category_view',
        'pur_category_add',
        'pur_category_edit',
        'pur_category_del',
    ),

    //产品类型
    'pur_type' => array(
        'pur_type_view',
        'pur_type_add',
        'pur_type_edit',
        'pur_type_del',
    ),

    //产品品牌
    'pur_brand' => array(
        'pur_brand_view',
        'pur_brand_add',
        'pur_brand_edit',
        'pur_brand_del',
    ),

    //主营行业
    'pur_industry' => array(
        'pur_industry_view',
        'pur_industry_add',
        'pur_industry_edit',
        'pur_industry_del',
    ),

    //栏目
    'pur_section' => array(
        'pur_section_view',
        'pur_section_add',
        'pur_section_edit',
        'pur_section_del',
    ),

    //资讯
    'pur_content' => array(
        'pur_content_view',
        'pur_content_add',
        'pur_content_edit',
        'pur_content_del',
    ),

    //广告位置
    'pur_adpos' => array(
        'pur_adpos_view',
        'pur_adpos_add',
        'pur_adpos_edit',
        'pur_adpos_del',
    ),
    //广告
    'pur_ad' => array(
        'pur_ad_view',
        'pur_ad_add',
        'pur_ad_edit',
        'pur_ad_del',
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

    'pur_business' => '商户管理',
    'pur_business_view' => '查看商户',
    'pur_business_edit' => '编辑商户信息',
    'pur_business_exam' => '商户审核',
    'pur_business_frozen' => '冻结商户/解除冻结',


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

    'pur_withdraw' => '提现管理',
    'pur_withdraw_view'=>'查看提现记录',
    'pur_withdraw_edit'=>'编辑提现状态',
    'pur_withdraw_del'=>'删除提现记录',
    'pur_withdraw_log'=>'查看提现日志',

    'pur_exchange' => '交易记录',
    'pur_exchange_view' => '查看交易记录',
    'pur_exchange_reward' => '佣金记录',

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
    'pur_product_view'=>'查看产品',
    'pur_product_exam'=>'审核产品',
//    'pur_product_edit'=>'编辑产品',
//    'pur_product_del'=>'删除产品',

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

    'pur_category' => '主营分类',
    'pur_category_view' => '查看主营分类',
    'pur_category_add' => '添加主营分类',
    'pur_category_edit' => '编辑主营分类',
    'pur_category_del' => '删除主营分类',

    'pur_type' => '产品类型',
    'pur_type_view' => '查看产品类型',
    'pur_type_add' => '添加产品类型',
    'pur_type_edit' => '编辑产品类型',
    'pur_type_del' => '删除产品类型',

    'pur_brand' => '产品品牌',
    'pur_brand_view' => '查看产品品牌',
    'pur_brand_add' => '添加产品品牌',
    'pur_brand_edit' => '编辑产品品牌',
    'pur_brand_del' => '删除产品品牌',

    'pur_industry' => '主营行业',
    'pur_industry_view' => '查看主营行业',
    'pur_industry_add' => '添加主营行业',
    'pur_industry_edit' => '编辑主营行业',
    'pur_industry_del' => '删除主营行业',

    'pur_section' => '栏目管理',
    'pur_section_view' => '查看栏目',
    'pur_section_add' => '添加栏目',
    'pur_section_edit' => '编辑栏目',
    'pur_section_del' => '删除栏目',

    'pur_content' => '资讯管理',
    'pur_content_view' => '查看资讯',
    'pur_content_add' => '添加资讯',
    'pur_content_edit' => '编辑资讯',
    'pur_content_del' => '删除资讯',

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
);

global $menus;
$menus = array(
    'pur_product' => array('url'=>'product.php', 'title'=>'产品管理', 'parent' => 'menu_product'),
    'pur_brand' => array('url'=>'brand.php', 'title'=>'产品品牌', 'parent'=>'menu_product'),
    'pur_type' => array('url'=>'type.php', 'title'=>'产品类型', 'parent'=>'menu_product'),
    'pur_category' => array('url'=>'category.php', 'title'=>'主营分类', 'parent'=>'menu_product'),
    'pur_industry' => array('url'=>'industry.php', 'title'=>'主营行业', 'parent'=>'menu_product'),
    'pur_member' => array('url'=>'member.php', 'title'=>'会员管理', 'parent' => 'menu_member'),
    'pur_business' => array('url' => 'business.php', 'title' => '商户管理', 'parent' => 'menu_business'),
    'pur_account' => array('url'=>'account.php', 'title'=>'账户明细', 'parent' => 'menu_account'),
    'pur_withdraw' => array('url'=>'withdraw.php', 'title'=>'提现管理', 'parent' => 'menu_account'),
    'pur_exchange' => array('url'=>'exchange.php', 'title'=>'交易管理', 'parent' => 'menu_account'),
    'pur_admin' => array('url'=>'admin.php', 'title'=>'管理员管理', 'parent' => 'menu_admin'),
    'pur_role' => array('url'=>'role.php', 'title'=>'管理员角色管理', 'parent' => 'menu_admin'),
    'pur_sysconf' => array('url'=>'sysconf.php', 'title'=>'参数设置', 'parent' => 'menu_site'),
    'pur_passwd' => array('url' => 'password.php', 'title' => '修改密码', 'parent' => 'memu_self'),
    'pur_adpos' => array('url' => 'adpos.php', 'title' => '广告位置管理', 'parent' => 'menu_site'),
    'pur_ad' => array('url' => 'ad.php', 'title' => '广告管理', 'parent' => 'menu_site'),
    'pur_stastics' => array('url' => 'stastics.php', 'title' => '查看统计', 'parent' => 'menu_stastics'),
    'pur_section' => array('url' => 'section.php', 'title' => '栏目管理', 'parent' => 'menu_cms'),
    'pur_content' => array('url' => 'content.php', 'title' => '资讯管理', 'parent' => 'menu_cms'),
);

global $topMenus;
$topMenus = array(
    'menu_site' => array('title' => '系统设置', 'icon' => '&#xe607;'),
    'menu_member' => array('title' => '会员管理', 'icon' => '&#xe603;'),
    'menu_business' => array('title' => '商户管理', 'icon' => '&#xe603;'),
    'menu_account' => array('title' => '财务管理', 'icon' => '&#xe603;'),
    'menu_product' => array('title' => '产品管理', 'icon' => '&#xe603;'),
    'menu_order' => array('title' => '订单管理', 'icon' => '&#xe603;'),
    'menu_admin' => array('title' => '权限管理', 'icon' => '&#xe601;'),
    'menu_self' => array('title' => '个人信息', 'icon' => '&#xe602;'),
    'menu_stastics' => array('title' => '站点统计', 'icon' => '&#xe604;'),
    'menu_cms' => array('title' => 'cms管理', 'icon' => '&#xe604;'),
);
