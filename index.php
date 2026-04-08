<?php

session_start();

$isUserLoggedIn = isset($_SESSION['userid']);

if ($isUserLoggedIn && !array_key_exists('isAdmin', $_SESSION)) {
    require_once 'app/models/userModel.php';
    $_SESSION['isAdmin'] = !empty(isAdmin((int)$_SESSION['userid']));
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
