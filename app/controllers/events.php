<?php
require_once 'app/models/eventsModel.php';

$show = 5;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['show']) && is_numeric($_GET['show'])) {
    $show = (int) $_GET['show'];
}

$events_to_display = getEventsByDateDesc($show);
$showMore = $show + 10;
$current_date = new DateTime(date('Y-m-d'));

$joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$moisFr  = [1 => 'Janvier', 2 => 'Fevrier', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Aout', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Decembre'];

$isLoggedIn = isset($_SESSION['userid']);
$todayMarked = false;
$eventsDisplay = [];

foreach ($events_to_display as $event) {
    $eventId = (int) $event['id_evenement'];
    $eventDateString = substr($event['date_evenement'], 0, 10);
    $eventDate = new DateTime($eventDateString);
    $eventDateInfo = getdate(strtotime($eventDateString));

    $otherClasses = '';
    $isPassed = false;
    $closestId = '';

    if ($eventDate < $current_date) {
        $datePinClass = 'passed';
        $datePinLabel = 'Passé';
        $otherClasses = 'passed';
        $isPassed = true;
    } elseif ($eventDate == $current_date) {
        $datePinClass = 'today';
        $datePinLabel = "Aujourd'hui";
        if (!$todayMarked) {
            $closestId = 'closest-event';
            $todayMarked = true;
        }
    } else {
        $datePinClass = 'upcoming';
        $datePinLabel = 'A venir';
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
        'data'         => $event,
        'dateInfo'     => $eventDateInfo,
        'otherClasses' => $otherClasses,
        'closestId'    => $closestId,
        'datePinClass' => $datePinClass,
        'datePinLabel' => $datePinLabel,
        'eventLabel'   => $eventLabel,
        'eventClass'   => $eventClass,
    ];
}

if (!$todayMarked && !empty($eventsDisplay)) {
    $eventsDisplay[0]['closestId'] = 'closest-event';
}

$events = getAllEventsForCalendar();

$calendarEvents = [];

foreach ($events as $event) {
    if ($event['deleted']) continue;
    $calendarEvents[] = [
        'title' => $event['nom_evenement'],
        'start' => substr($event['date_evenement'], 0, 10),
        'allDay' => true,
        'url' =>  'index.php?page=event_details&id=' . $event['id_evenement'],
        'extendedProps' => [
            'type' => $event['type_evenement'] ?? 'autre',
            'location' => $event['lieu_evenement'] ?? ''
        ],
        'backgroundColor' => '#f59e0b',
        'borderColor' => '#f59e0b',
        'textColor' => '#ffffff'
    ];
}

require_once 'app/views/events.php';
