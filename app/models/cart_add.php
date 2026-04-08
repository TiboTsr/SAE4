<?php

require_once __DIR__ . '/../../core/DB.php';
require_once __DIR__ . '/Cart.php';

$db = new DB();
$cart = new Cart($db);

$json = array('error' => true);

if (isset($_GET['id'])) {
    $product = $db->select(
        "SELECT id_article FROM ARTICLE WHERE id_article = ?",
        "i",
        [$_GET['id']]
    );

    if (empty($product)) {
        $json['message'] = "Ce produit n'existe pas";
    } else {
        $cart->add($product[0]['id_article']);
        $json['error'] = false;
        $json['total'] = $cart->total();
        $json['count'] = $cart->count();
        $json['message'] = "Le produit a bien ete ajoute a votre panier";
    }
} else {
    $json['message'] = "Vous n'avez pas ajoute de produit a ajouter au panier";
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($json);
