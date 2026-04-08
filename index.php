<?php

session_start();

$isUserLoggedIn = isset($_SESSION['userid']);

if ($isUserLoggedIn) {
    require_once 'app/models/userModel.php';
    $isAdminFromDb = !empty(isAdmin((int)$_SESSION['userid']));

    // Évite qu'un ancien état de session masque le bouton admin.
    if ($isAdminFromDb) {
        $_SESSION['isAdmin'] = true;
    } elseif (!array_key_exists('isAdmin', $_SESSION)) {
        $_SESSION['isAdmin'] = false;
    }
}

$isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];

if (isset($_GET['page'])) {
    $page = $_GET['page'];

    if (preg_match('/^[a-z_]+$/i', $page) && file_exists('app/controllers/' . $page . '.php')) {
        require 'app/controllers/' . $page . '.php';
        return;
    }
}

require 'app/controllers/home.php';
