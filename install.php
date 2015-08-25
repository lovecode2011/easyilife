<?php
/**
 * 初始化数据库
 */
include 'library/init.inc.php';

header('Content-Type: text/html;charset=utf-8');
$table = array();
$sql = array();

$table[] = '产品分类';
$sql[] = 'create table if not exists '.$db->table('category').' (
    `id` bigint not null auto_increment primary key,
    `name` varchar(255) not null,
    `business_account` varchar(255) not null,
    `parent_id` int not null,
    `path` varchar(255),
    `price_filter` int not null default \'3\',
    `icon` varchar(255),
    `search_brand` tinyint(1) not null default \'1\'
) default charset=utf8;';

$table[] = '产品类型';
$sql[] = 'create table if not exists '.$db->table('product_type').' (
    `id` bigint not null auto_increment primary key,
    `name` varchar(255) not null
) default charset=utf8;';

$table[] = '产品类型属性';
$sql[] = 'create table if not exists '.$db->table('product_attributes').' (
    `id` bigint not null auto_increment primary key,
    `product_type_id` int not null,
    `type` varchar(255) not null,
    `options` text,
    `name` varchar(255) not null,
    `needle` tinyint(1) not null default \'1\',
    `filter` tinyint(1) not null default \'0\'
) default charset=utf8;';

$table[] = '产品相册';
$sql[] = 'create table if not exists '.$db->table('gallery').' (
    `id` bigint not null auto_increment primary key,
    `big_img` varchar(255) not null,
    `thumb_img` varchar(255) not null,
    `original_img` varchar(255) not null,
    `product_sn` varchar(255) not null,
    `order_view` int not null,
    index (`product_sn`)
) default charset=utf8;';

$table[] = '产品品牌';
$sql[] = 'create table if not exists '.$db->table('brand').' (
    `id` bigint not null auto_increment primary key,
    `name` varchar(255) not null,
    `img` varchar(255) not null,
    `desc` text
) default charset=utf8;';

$table[] = '库存';
$sql[] = 'create table if not exists '.$db->table('inventory').' (
    `id` bigint not null auto_increment primary key,
    `product_sn` varchar(255) not null,
    `attributes` varchar(255) not null,
    `inventory` int not null default \'0\',
    `inventory_await` int not null default \'0\',
    `inventory_logic` int not null default \'0\',
    index (`product_sn`),
    index (`attributes`)
) default charset=utf8;';

$table[] = '产品评价';
$sql[] = 'create table if not exists '.$db->table('comment').' (
    `id` bigint not null auto_increment primary key,
    `add_time` int not null,
    `comment` varchar(255) not null,
    `star` int not null default \'5\',
    `img` varchar(255),
    `product_sn` varchar(255) not null,
    `account` varchar(255) not null,
    `last_modify` timestamp,
    `path` varchar(255),
    `parent_id` int not null default \'0\'
) default charset=utf8;';

$table[] = '产品收藏';
$sql[] = 'create table if not exists '.$db->table('collection').' (
    `product_sn` varchar(255) not null,
    `account` varchar(255) not null,
    `add_time` int not null,
    primary key (`product_sn`,`account`)
) default charset=utf8;';

$table[] = '产品分销';
$sql[] = 'create table if not exists '.$db->table('distribution').' (
    `product_sn` varchar(255) not null,
    `account` varchar(255) not null,
    `add_time` int not null,
    primary key (`product_sn`,`account`)
) default charset=utf8;';

$table[] = '砍价记录';
$sql[] = 'create table if not exists '.$db->table('discount').' (
    `id` bigint not null auto_increment primary key,
    `product_sn` varchar(255) not null,
    `account` varchar(255) not null,
    `reduce` decimal(18,2) not null,
    `owner` varchar(255) not null,
    `add_time` int not null,
    index (`product_sn`),
    index (`owner`)
) default charset=utf8;';

$table[] = '提现申请';
$sql[] = 'create table if not exists '.$db->table('withdraw').' (
    `withdraw_sn` varchar(255) not null primary key,
    `account` varchar(255) not null,
    `amount` decimal(18,2) not null,
    `fee` decimal(18,2) not null,
    `status` int not null default \'0\',
    `add_time` int not null,
    `solve_time` int,
    `remark` varchar(255),
    `bank` varchar(255) not null,
    `bank_account` varchar(255) not null,
    `bank_card` varchar(255) not null
) default charset=utf8;';

$table[] = '提现操作记录';
$sql[] = 'create table if not exists '.$db->table('withdraw_log').' (
    `id` bigint not null auto_increment primary key,
    `add_time` int not null,
    `operator` varchar(255) not null,
    `withdraw_sn` varchar(255) not null,
    `status` int not null,
    `remark` varchar(255),
    index (`withdraw_sn`)
) default charset=utf8;';

$table[] = '商家提现申请';
$sql[] = 'create table if not exists '.$db->table('business_withdraw').' (
    `withdraw_sn` varchar(255) not null primary key,
    `business_account` varchar(255) not null,
    `amount` decimal(18,2) not null,
    `fee` decimal(18,2) not null,
    `status` int not null default \'0\',
    `add_time` int not null,
    `solve_time` int,
    `remark` varchar(255),
    `bank` varchar(255) not null,
    `bank_account` varchar(255) not null,
    `bank_card` varchar(255) not null
) default charset=utf8;';

$table[] = '商家提现操作记录';
$sql[] = 'create table if not exists '.$db->table('business_withdraw_log').' (
    `id` bigint not null auto_increment primary key,
    `add_time` int not null,
    `operator` varchar(255) not null,
    `withdraw_sn` varchar(255) not null,
    `status` int not null,
    `remark` varchar(255),
    index (`withdraw_sn`)
) default charset=utf8;';

$table[] = '商家';
$sql[] = 'create table if not exists '.$db->table('business').' (
    `id` bigint not null auto_increment unique,
    `business_account` varchar(255) not null primary key,
    `password` varchar(255) not null,
    `license` varchar(255) not null,
    `identity` varchar(255) not null,
    `company` varchar(255) not null,
    `industry_id` int not null,
    `category_id` int not null,
    `province` int not null,
    `city` int not null,
    `district` int not null,
    `group` int not null,
    `address` varchar(255) not null,
    `desc` varchar(255),
    `status` int not null,
    `contact` varchar(255) not null,
    `mobile` varchar(255) not null,
    `email` varchar(255) not null,
    `shop_name` varchar(255),
    `shop_logo` varchar(255),
    `comment` int not null,
    `is_recommend` tinyint(1) not null default \'0\',
    `balance` decimal(18,2) not null default \'0\',
    `trade` decimal(18,2) not null default \'0\',
    `longitude` varchar(255),
    `latitude` varchar(255),
    `expired` int not null default \'0\',
    `account` varchar(255) not null
) default charset=utf8;';

$table[] = '商家交易';
$sql[] = 'create table if not exists '.$db->table('trade').' (
    `id` bigint not null auto_increment primary key,
    `business_account` varchar(255) not null,
    `trade` decimal(18,2) not null,
    `status` int not null default \'0\',
    `remark` varchar(255),
    `settle_time` int not null,
    `solve_time` int,
    index (`business_account`)
) default charset=utf8;';

$table[] = '商家明细';
$sql[] = 'create table if not exists '.$db->table('business_exchange_log').' (
    `id` bigint not null auto_increment primary key,
    `business_account` varchar(255) not null,
    `balance` decimal(18,2) not null default \'0\',
    `trade` decimal(18,2) not null default \'0\',
    `add_time` int not null,
    `remark` varchar(255),
    `operator` varchar(255) not null,
    index (`business_account`)
) default charset=utf8;';

$table[] = '会员账户明细';
$sql[] = 'create table if not exists '.$db->table('member_exchange_log').' (
    `id` bigint not null auto_increment primary key,
    `account` varchar(255) not null,
    `add_time` int not null,
    `balance` decimal(18,2) not null default \'0\',
    `reward` decimal(18,2) not null default \'0\',
    `integral` decimal(18,2) not null default \'0\',
    `integral_await` decimal(18,2) not null default \'0\',
    `reward_await` decimal(18,2) not null default \'0\',
    `operator` varchar(255) not null,
    `remark` varchar(255)
) default charset=utf8;';

$table[] = '会员';
$sql[] = 'create table if not exists '.$db->table('member').' (
    `id` bigint not null auto_increment unique,
    `account` varchar(255) not null primary key,
    `mobile` varchar(255),
    `password` varchar(255),
    `openid` varchar(255),
    `sex` char(2),
    `nickname` varchar(255),
    `province` int,
    `city` int,
    `headimg` varchar(255),
    `add_time` int not null,
    `leave_time` int,
    `status` int not null default \'1\',
    `longitude` varchar(255),
    `latitude` varchar(255),
    `unionid` varchar(255),
    `email` varchar(255),
    `integral` decimal(18,2) not null default \'0\',
    `reward` decimal(18,2) not null default \'0\',
    `balance` decimal(18,2) not null default \'0\',
    `reward_await` decimal(18,2) not null default \'0\',
    `integral_await` decimal(18,2) not null default \'0\',
    `integral_amount` decimal(18,2) not null default \'0\',
    `reward_amount` decimal(18,2) not null default \'0\',
    `parent_id` int not null default \'0\',
    `path` varchar(255),
    `business_account` varchar(255)
) default charset=utf8;';

$table[] = '订单';
$sql[] = 'create table if not exists '.$db->table('order').' (
    `id` bigint not null auto_increment unique,
    `order_sn` varchar(255) not null primary key,
    `add_time` int not null,
    `delivery_time` int,
    `receive_time` int,
    `status` int not null default \'0\',
    `amount` decimal(18,2) not null,
    `integral_amount` decimal(18,2) not null,
    `delivery_id` int not null,
    `delivery_name` varchar(255),
    `business_account` varchar(255) not null,
    `delivery_fee` decimal(18,2) not null,
    `product_amount` decimal(18,2) not null,
    `integral_given_amount` decimal(18,2) not null,
    `payment_id` int not null,
    `payment_name` varchar(255),
    `payment_sn` varchar(255),
    `express_id` int,
    `express_sn` varchar(255),
    `remark` varchar(255),
    `address_id` int not null default \'0\',
    `province` int not null,
    `city` int not null,
    `district` int not null,
    `group` int not null,
    `address` varchar(255) not null,
    `consignee` varchar(255) not null,
    `mobile` varchar(255) not null,
    `zipcode` varchar(255),
    `reward_amount` decimal(18,2) not null,
    `account` varchar(255) not null,
    `self_delivery` tinyint(1) not null default \'0\',
    `pay_time` int,
    index (`business_account`)
) default charset=utf8;';

$table[] = '订单操作记录';
$sql[] = 'create table if not exists '.$db->table('order_log').' (
    `id` bigint not null auto_increment primary key,
    `order_sn` varchar(255) not null,
    `operator` varchar(255) not null,
    `status` int not null,
    `add_time` int not null,
    `remark` varchar(255)
) default charset=utf8;';

$table[] = '配送方式';
$sql[] = 'create table if not exists '.$db->table('delivery').' (
    `id` bigint not null auto_increment primary key,
    `self_delivery` tinyint(1) not null default \'0\',
    `name` varchar(255) not null,
    `business_account` varchar(255) not null,
    `plugins` varchar(255) not null,
    `desc` varchar(255),
    `status` int not null default \'0\',
    index (`business_account`)
) default charset=utf8;';

$table[] = '配送区域';
$sql[] = 'create table if not exists '.$db->table('delivery_area').' (
    `id` bigint not null auto_increment primary key,
    `first_weight` decimal(18,2) not null,
    `next_weight` decimal(18,2) not null,
    `free` decimal(18,2) not null default \'0\',
    `delivery_id` int not null,
    `name` varchar(255),
    index (`delivery_id`)
) default charset=utf8;';

$table[] = '配送区域映射';
$sql[] = 'create table if not exists '.$db->table('delivery_area_mapper').' (
    `id` bigint not null auto_increment primary key,
    `area_id` int not null,
    `province` int not null,
    `city` int not null,
    `district` int not null
) default charset=utf8;';

$table[] = '支付方式';
$sql[] = 'create table if not exists '.$db->table('payment').' (
    `id` bigint not null auto_increment primary key,
    `name` varchar(255) not null,
    `plugins` varchar(255) not null,
    `configure` text,
    `desc` varchar(255),
    `status` int not null default \'0\'
) default charset=utf8;';

$table[] = '广告位置';
$sql[] = 'create table if not exists '.$db->table('ad_position').' (
    `id` bigint not null auto_increment primary key,
    `name` varchar(255) not null,
    `width` varchar(255) not null,
    `height` varchar(255) not null,
    `number` int not null default \'1\',
    `code` text
) default charset=utf8;';

$table[] = '广告';
$sql[] = 'create table if not exists '.$db->table('ad').' (
    `id` bigint not null auto_increment primary key,
    `img` varchar(255) not null,
    `alt` varchar(255) not null,
    `forever` tinyint(1) not null default \'1\',
    `click_count` int not null default \'0\',
    `url` varchar(255) not null,
    `order_view` int not null default \'50\',
    `ad_position_id` int not null,
    `start_time` int,
    `end_time` int,
    `add_time` int not null
) default charset=utf8;';

$table[] = '管理员';
$sql[] = 'create table if not exists '.$db->table('admin').' (
    `account` varchar(255) not null primary key,
    `password` varchar(255) not null,
    `email` varchar(255) not null,
    `name` varchar(255) not null,
    `role_id` int not null,
    `mobile` varchar(255) not null,
    `sex` char(2) not null,
    `business_account` varchar(255) not null,
    index (`business_account`)
) default charset=utf8;';

$table[] = '角色';
$sql[] = 'create table if not exists '.$db->table('role').' (
    `id` bigint not null auto_increment primary key,
    `name` varchar(255) not null,
    `purview` text not null,
    `business_account` varchar(255) not null,
    index (`business_account`)
) default charset=utf8;';

$table[] = '站点参数';
$sql[] = 'create table if not exists '.$db->table('sysconf').' (
    `key` varchar(255) not null primary key,
    `name` varchar(255) not null,
    `value` varchar(255),
    `type` varchar(255) not null,
    `options` text,
    `group` varchar(255) not null,
    `remark` varchar(255) not null
) default charset=utf8;';

$table[] = '购物车';
$sql[] = 'create table if not exists '.$db->table('cart').' (
    `id` bigint not null auto_increment primary key,
    `openid` varchar(255) not null,
    `account` varchar(255),
    `product_sn` varchar(255) not null,
    `price` decimal(18,2) not null,
    `integral` decimal(18,2) not null,
    `number` int not null,
    `business_account` varchar(255) not null,
    `add_time` int not null,
    index (`openid`),
    index (`account`),
    index (`business_account`)
) default charset=utf8;';

$table[] = '支付记录';
$sql[] = 'create table if not exists '.$db->table('pay_log').' (
    `pay_sn` varchar(255) not null primary key,
    `amount` decimal(18,2) not null,
    `status` int not null,
    `order_assoc` varchar(255) not null,
    `id` bigint not null auto_increment unique,
    `pay_time` int,
    `add_time` int not null
) default charset=utf8;';

$table[] = '会员佣金';
$sql[] = 'create table if not exists '.$db->table('member_reward').' (
    `id` bigint not null auto_increment unique,
    `remark` varchar(255),
    `integral` decimal(18,2) not null default \'0\',
    `reward` decimal(18,2) not null default \'0\',
    `status` int not null,
    `settle_time` int not null,
    `solve_time` int,
    `account` varchar(255) not null
) default charset=utf8;';

$table[] = '产品';
$sql[] = 'create table if not exists '.$db->table('product').' (
    `id` bigint not null auto_increment unique,
    `product_sn` varchar(255) not null primary key,
    `name` varchar(255) not null,
    `shop_price` decimal(18,2) not null,
    `price` decimal(18,2) not null,
    `lowest_price` decimal(18,2) not null,
    `reward` decimal(18,2) not null,
    `integral` decimal(18,2) not null,
    `integral_given` decimal(18,2) not null,
    `img` varchar(255) not null,
    `desc` varchar(255),
    `detail` text,
    `business_account` varchar(255) not null,
    `category_id` int not null,
    `product_type_id` int not null,
    `status` int not null default \'0\',
    `promote_price` decimal(18,2) not null,
    `promote_begin` int,
    `promote_end` int,
    `add_time` int not null,
    `weight` int not null,
    `collection_count` int not null default \'0\',
    `comment_count` int not null default \'0\',
    `star` int not null default \'0\',
    `distribution_count` int not null default \'0\',
    `brand_id` int not null,
    `up` int not null default \'0\',
    `share_count` int not null default \'0\',
    `sale_count` int not null default \'0\',
    `order_view` int not null default \'50\',
    `free_delivery` tinyint(1) not null default \'0\'
) default charset=utf8;';

$table[] = '短链接';
$sql[] = 'create table if not exists '.$db->table('short_link').' (
    `url` varchar(255) not null primary key,
    `hash` varchar(255) not null unique
) default charset=utf8;';


$table[] = '省';
$sql[] = 'create table if not exists '.$db->table('province').' (
    `id` int not null auto_increment primary key,
    `province_name` varchar(255) not null
) default charset=utf8;';

$table[] = '市';
$sql[] = 'create table if not exists '.$db->table('city').' (
    `id` int not null auto_increment primary key,
    `city_name` varchar(255) not null,
    `province_id` int not null,
    `first_letter` char(1) not null,
    `is_hot` tinyint not null default 0,
    `code` varchar(255) not null
) default charset=utf8;';

$table[] = '区';
$sql[] = 'create table if not exists '.$db->table('district').' (
    `id` int not null auto_increment primary key,
    `district_name` varchar(255) not null,
    `city_id` int not null,
    `code` varchar(255) not null
) default charset=utf8;';

$table[] = '商圈';
$sql[] = 'create table if not exists '.$db->table('group').' (
    `id` int not null auto_increment primary key,
    `group_name` varchar(255) not null,
    `district_id` int not null,
    `city_id` int not null,
    `code` varchar(255) not null,
    `first_letter` char(1) not null
) default charset=utf8;';

$table[] = '主营行业';
$sql[] = 'create table if not exists '.$db->table('industry').' (
    `id` int not null auto_increment primary key,
    `name` varchar(255) not null
) default charset=utf8;';

$table[] = '主营分类';
$sql[] = 'create table if not exists '.$db->table('classification').' (
    `id` int not null auto_increment primary key,
    `name` varchar(255) not null
) default charset=utf8;';

$table[] = '商家认证信息';
$sql[] = 'create table if not exists '.$db->table('auth').' (
    `id` int not null auto_increment primary key,
    `business_account` varchar(255) not null,
    `company` varchar(255) not null,
    `license` varchar(255) not null,
    `identity` varchar(255) not null,
    `industry_id` int not null,
    `category_id` int not null,
    `contact` varchar(255) not null,
    `mobile` varchar(255) not null,
    `email` varchar(255) not null,
    `add_time` int not null,
    `status` tinyint not null default 0
) default charset=utf8;';



echo '创建数据库表:<br/>';
foreach($table as $key=>$name)
{
    echo $name;

    $dot_count = 40 - mb_strlen($name)*3;
    while($dot_count--)
    {
        echo '-';
    }
    
    if($db->query($sql[$key]))
    {
        echo ' <font color="green">success</font><br/>';
    } else {
        echo ' <font color="red">fail</font>:'.$db->errmsg().'<br/>';
    }
}
