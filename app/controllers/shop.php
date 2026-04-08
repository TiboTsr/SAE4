<?php
require_once 'core/DB.php';
require_once 'app/models/shopModel.php';
require_once 'app/models/files_save.php';
require_once 'app/models/Cart.php';

$db   = new DB();
$cart = new Cart($db);

$filters    = [];
$orderBy    = 'name_asc';
$searchTerm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        $filters    = [];
        $orderBy    = 'name_asc';
        $searchTerm = '';
    } else {
        if (isset($_POST['category'])) {
            $filters = $_POST['category'];
        }
        if (isset($_POST['sort'])) {
            $orderBy = $_POST['sort'];
        }
        if (!empty($_POST['search'])) {
            $searchTerm = $_POST['search'];
        }
    }
}

$products = getFilteredProducts($searchTerm, $filters, $orderBy);

require_once 'app/views/shop.php';