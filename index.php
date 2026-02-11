<?php
session_start();

if(isset($_GET['page'])  && file_exists('app/controllers/'.$_GET['page'].'.php')) {
    require 'app/controllers/'.$_GET['page'].'.php';
}else {
    require 'app/controllers/home.php';
}
