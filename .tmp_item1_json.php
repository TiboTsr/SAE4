<?php
require_once 'api/DB.php';
require_once 'api/models/Item.php';
use model\Item;

$item = Item::getInstance(1);
header('Content-Type: application/json');
echo json_encode($item, JSON_PRETTY_PRINT);
