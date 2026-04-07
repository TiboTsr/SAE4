<?php
require_once 'api/DB.php';
require_once 'api/models/Item.php';

use model\Item;

$items = Item::bulkFetch();
foreach ($items as $it) {
    echo $it['id_article'] . "\t" . $it['nom_article'] . "\t" . $it['image_article'] . PHP_EOL;
}
