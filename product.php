<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/9/7
 * Time: 上午11:22
 */
include 'library/init.inc.php';

$id = intval(getGET('id'));

if($id <= 0)
{
    redirect('index.php');
}

$get_product = 'select * from '.$db->table('product').' where `id`='.$id;

$product = $db->fetchRow($get_product);

if($product)
{
    $product['gallery'] = array($product['img']);
    $product_sn = $product['product_sn'];

    $get_gallery = 'select `thumb_img` from '.$db->table('gallery').' where `product_sn`=\''.$product_sn.'\'';
    $gallery = $db->fetchAll($get_gallery);

    if($gallery)
    {
        $product['gallery'] = array_merge($product['gallery'], $gallery);
    }

    $get_product_attributes = 'select `id`,`name` from '.$db->table('product_attributes').' where `product_type_id`='.$product['product_type_id'];

    $attributes = $db->fetchAll($get_product_attributes);

    $get_inventory = 'select `attributes`,`inventory`,`inventory_await`,`inventory_logic` from '.$db->table('inventory').' where `product_sn`=\''.$product_sn.'\'';
    $inventory = $db->fetchAll($get_inventory);

    foreach($inventory as $inventory_tmp)
    {
        
    }
} else {
    redirect('index.php');
}

assign('product', $product);
$smarty->display('product.phtml');