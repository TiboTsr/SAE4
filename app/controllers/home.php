<?php
require_once 'app/models/userModel.php';
require_once 'app/models/eventsModel.php';

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

require_once 'app/views/home.php';