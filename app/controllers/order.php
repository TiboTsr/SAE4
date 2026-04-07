<?php

require_once 'app/models/shopModel.php';
require_once 'app/models/files_save.php';
require_once 'app/models/Cart.php';



// Initialisation du panier
$cart = new Cart();



$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: index.php?page=login");
    exit;
}

$userid = $_SESSION["userid"];

// Récupérer le panier
if (empty($_SESSION['cart'])) {
    header("Location: index.php?page=cart");
    exit;
}

// Calculer le total de la commande
$total = 0;
$cart = $_SESSION['cart'];
$products = getArticle($cart);

$cart_items = [];
foreach ($products as $product) {
    if(
        $product['stock_article'] > 0 && $_SESSION['cart'][$product['id_article']] > $product['stock_article']
    ){
        $cart[$product['id_article']] = $product['stock_article'];
    }
    $cart_items[$product['id_article']] = [
        'nom_article' => $product['nom_article'], // Ajout du nom de l'article
        'prix_article' => $product['prix_article'],
        'quantite' => $cart[$product['id_article']],
    ];
    $total += $product['prix_article'] * $cart[$product['id_article']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['mode_paiement']) && !empty($_POST['mode_paiement'])) {
        $allowedPaymentModes = ['carte_credit', 'paypal'];
        $mode_paiement = $_POST['mode_paiement'];

        if (!in_array($mode_paiement, $allowedPaymentModes, true)) {
            $_SESSION['message'] = "Mode de paiement invalide.";
            $_SESSION['message_type'] = "error";
            header("Location: index.php?page=order");
            exit;
        }

        // Enregistrer la commande dans la base de données
        foreach ($cart_items as $product_id => $item) {
            purchaseItem($userid, $product_id, $item['quantite'], $mode_paiement);
        }

        $_SESSION['cart'] = [];
        
        $_SESSION['message'] = "Commande réalisée avec succès !";
        $_SESSION['message_type'] = "success";

        header("Location: index.php?page=cart");
        exit;
    }
}

if (!empty($_SESSION['userid'])) {
    // Vérifie l'adhésion de l'utilisateur
    $adherant = getReduction($userid);
    
    //récupérer la réduction liée au grade
    if (!empty($adherant)) {
        $reductionGrade = floatval($adherant[0]["reduction_grade"] ?? 0);
        $user_reduction = 1 - ($reductionGrade / 100);
        $totalWithReduc = 0;

        // Calcule le total en tenant compte des réductions applicables
        foreach ($products as $product) {
            if (!empty($product['reduction_article'])) { // Vérifie si une réduction est applicable
                $totalWithReduc += $product['prix_article'] * $cart[$product['id_article']] * $user_reduction;
            } else {
                $totalWithReduc += $product['prix_article'] * $cart[$product['id_article']];
            }
        }       
    }
}


require 'app/views/order.php';
