<?php

require_once 'app/models/Database.php';
require_once 'app/models/eventsModel.php';

$db = new Database();
$isLoggedIn = isset($_SESSION["userid"]);

$podium = $db->select("SELECT prenom_membre, xp_membre, pp_membre FROM MEMBRE ORDER BY xp_membre DESC LIMIT 3;");

$date = getdate();
$sql_date = $date["year"]."-".$date["mon"]."-".$date["mday"];
$events_to_display = $db->select(
    "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement >= ? ORDER BY date_evenement ASC LIMIT 2;",
    "s",
    [$sql_date]
);

$events = getEventsToDisplay($sql_date);

// Préparer les labels et classes pour chaque événement
$eventsDisplay = [];
$isLoggedIn = isset($_SESSION['userid']);

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
        'data' => $event,
        'label' => $eventLabel,
        'class' => $eventClass
    ];
}

require_once 'app/views/home.php';