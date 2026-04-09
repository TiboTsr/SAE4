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
$availableCategories = getProductCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        $filters    = [];
        $orderBy    = 'name_asc';
        $searchTerm = '';
    } else {
        if (isset($_POST['category']) && is_array($_POST['category'])) {
            $rawFilters = array_values(
                array_filter(
                    array_map(static fn($value): string => trim((string)$value), $_POST['category']),
                    static fn(string $value): bool => $value !== ''
                )
            );

            if (!empty($availableCategories)) {
                $filters = array_values(array_intersect($rawFilters, $availableCategories));
            }
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
