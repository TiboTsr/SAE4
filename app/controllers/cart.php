<?php

require_once 'app/models/Cart.php';
require_once 'app/models/files_save.php';

$cart = new Cart();

$products = $cart->getProducts();
$totalWithReduc = !empty($_SESSION['userid'])
    ? $cart->getTotalWithReduction($_SESSION['userid'], $products)
    : null;

$message = $_SESSION['message'] ?? null;
$messageType = $_SESSION['message_type'] ?? null;
unset($_SESSION['message'], $_SESSION['message_type']);

require_once 'app/views/cart.php';
