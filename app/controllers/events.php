<?php
require_once 'app/models/eventsModel.php';

$show = 5;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['show']) && is_numeric($_GET['show'])) {
    $show = (int) $_GET['show'];
}

$date = getdate();
$sql_date = $date['year'] . '-' . $date['mon'] . '-' . $date['mday'];
$current_date = new DateTime(date('Y-m-d'));

$joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$moisFr  = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];

$future_events = getAllEventsToDisplay($sql_date);
$passed_events = getPassedEventsToDisplay($sql_date, $show);
$events_to_display = array_merge($passed_events, $future_events);

// Préparer les données d'affichage pour chaque événement
$isLoggedIn = isset($_SESSION['userid']);
$closest_event_id_set = false;
$eventsDisplay = [];

foreach ($events_to_display as $event) {
    $eventId   = $event['id_evenement'];
    $eventDate = new DateTime(substr($event['date_evenement'], 0, 10));
    $eventDateInfo = getdate(strtotime(substr($event['date_evenement'], 0, 10)));

    $otherClasses  = '';
    $isPassed      = false;
    $closestId     = '';

    if ($eventDate < $current_date) {
        $datePinClass = 'passed';
        $datePinLabel = 'Passé';
        $otherClasses = 'passed';
        $isPassed     = true;
    } elseif ($eventDate == $current_date) {
        $datePinClass = 'today';
        $datePinLabel = "Aujourd'hui";
        if (!$closest_event_id_set) {
            $closestId = 'closest-event';
            $closest_event_id_set = true;
        }
    } else {
        $datePinClass = 'upcoming';
        $datePinLabel = 'A venir';
        if (!$closest_event_id_set) {
            $closestId = 'closest-event';
            $closest_event_id_set = true;
        }
    }

    $isUnlimitedEvent = isUnlimitedEvent($eventId);
    $isPlaceAvailable = $isUnlimitedEvent || isPlaceAvailable($eventId);
    $eventLabel = $isPlaceAvailable ? "S'inscrire" : 'Complet';
    $eventClass = $isPlaceAvailable ? 'event-not-subscribed hover_effect' : 'event-full';

    if ($isLoggedIn && isUserSubscribed($_SESSION['userid'], $eventId)) {
        $eventLabel = 'Inscrit';
        $eventClass = 'event-subscribed';
    }

    if ($isPassed) {
        $eventLabel = 'Passé';
        $eventClass = 'event-full';
    }

    $eventsDisplay[] = [
        'data'          => $event,
        'dateInfo'      => $eventDateInfo,
        'otherClasses'  => $otherClasses,
        'closestId'     => $closestId,
        'datePinClass'  => $datePinClass,
        'datePinLabel'  => $datePinLabel,
        'eventLabel'    => $eventLabel,
        'eventClass'    => $eventClass,
    ];
}

require_once 'app/views/events.php';
