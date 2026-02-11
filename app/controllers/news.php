<?php

require_once 'app/models/newsModel.php';

$show = 5;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['show']) && is_numeric($_GET['show'])) {
    $show = (int) $_GET['show'];
}

$events_to_display = getNews($show);

require_once 'app/views/news.php';