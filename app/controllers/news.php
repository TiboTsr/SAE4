<?php

require_once 'app/models/newsModel.php';

$show = 5;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['show']) && is_numeric($_GET['show'])) {
    $show = (int) $_GET['show'];
}

$events_to_display = getNews($show);
$showMore = $show + 10;

$joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$moisFr = [1 => 'Janvier', 2 => 'Fevrier', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Aout', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Decembre'];
$today = new DateTime(date("Y-m-d"));

$newsItems = [];
$todayMarked = false;

foreach ($events_to_display as $index => $event) {
    $eventDateString = substr($event['date_actualite'], 0, 10);
    $eventDate = new DateTime($eventDateString);
    $eventDateInfo = getdate(strtotime($eventDateString));

    $isClosest = false;
    if (!$todayMarked && $eventDate == $today) {
        $isClosest = true;
        $todayMarked = true;
    }

    $newsItems[] = [
        'id_actualite' => $event['id_actualite'],
        'titre_actualite' => $event['titre_actualite'],
        'date_label' => ucwords($joursFr[$eventDateInfo['wday']] . " " . $eventDateInfo["mday"] . " " . $moisFr[$eventDateInfo['mon']]),
        'is_closest' => $isClosest,
        'is_first' => $index === 0
    ];
}

if (!$todayMarked && !empty($newsItems)) {
    $newsItems[0]['is_closest'] = true;
}

require_once 'app/views/news.php';
