<?php
require_once 'app/models/userModel.php';
require_once 'app/models/eventsModel.php';

$isLoggedIn = isset($_SESSION["userid"]);

$podium = getPodium();

foreach ([2,1,3] as $member_number):
    $pod = $podium[$member_number-1];

$date = getdate();
$sql_date = $date["year"]."-".$date["mon"]."-".$date["mday"];
$events_to_display = getEventsToDisplay($sql_date);

// Préparer les labels et classes pour chaque événement
$eventsDisplay = [];
$isLoggedIn = isset($_SESSION['userid']);

foreach ($events_to_display as $event) {
    $eventsDisplay[] = [
        'id' => $event['id_evenement'],
        'lieu' => $event['lieu_evenement'],
        'titre' => $event['nom_evenement'],
        'isPlaceDisponible' => isPlaceAvailable($eventId),
        'isSubscribed' => isUserSubscribed($_SESSION['userid'], $eventId)
    ];
}
endforeach;

$moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];

$event_date = substr($event['date_evenement'], 0, 10);
$event_date_info = getdate(strtotime($event_date));

require_once 'app/views/home.php';