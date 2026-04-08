<?php

require_once 'app/models/newsModel.php';
require_once 'app/models/files_save.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $eventid = $_GET['id'];
    $event = getNew($eventid);

    if (empty($event) || is_null($event)) {
        header("Location: index.php");
        exit;
    }

    $event = $event[0];

    $adjacentNewsIds = getAdjacentNewsIds((int) $event['id_actualite'], $event['date_actualite']);
    $olderNewsId = $adjacentNewsIds['older'];
    $newerNewsId = $adjacentNewsIds['newer'];
} else {
    header("Location: index.php");
    exit;
}


$isLoggedIn = isset($_SESSION["userid"]);

require_once 'app/views/news_details.php';
