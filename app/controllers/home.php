<?php
require_once 'app/models/userModel.php';
require_once 'app/models/eventsModel.php';
require_once 'app/models/files_save.php';

$isLoggedIn = isset($_SESSION['userid']);

$podium = getPodium();

$date = getdate();
$sql_date = $date['year'] . '-' . $date['mon'] . '-' . $date['mday'];

$events = getEventsToDisplay($sql_date);

$eventsDisplay = [];
foreach ($events as $event) {
    $eventId = $event['id_evenement'];
    $isPlaceAvailable = isPlaceAvailable($eventId);
    $eventLabel = $isPlaceAvailable ? "S'inscrire" : "Complet";
    $eventClass = $isPlaceAvailable ? "event-not-subscribed hover_effect" : "event-full";

    if ($isLoggedIn && isUserSubscribed($_SESSION['userid'], $eventId)) {
        $eventLabel = "Inscrit";
        $eventClass = "event-subscribed";
    }

    $eventsDisplay[] = [
        'data'  => $event,
        'label' => $eventLabel,
        'class' => $eventClass,
    ];
}

$moisFr = [
    1 => "janvier",
    2 => "février",
    3 => "mars",
    4 => "avril",
    5 => "mai",
    6 => "juin",
    7 => "juillet",
    8 => "août",
    9 => "septembre",
    10 => "octobre",
    11 => "novembre",
    12 => "décembre"
];

require_once 'app/views/home.php';