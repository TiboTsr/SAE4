<?php
session_start();

$isUserLoggedIn = isset($_SESSION['userid']);
$isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ;

if(isset($_GET['page'])  && file_exists('app/controllers/'.$_GET['page'].'.php')) {
    require 'app/controllers/'.$_GET['page'].'.php';
}else {
    require 'app/controllers/home.php';
}

