<?php

require_once 'app/models/eventsModel.php';

$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: index.php?page=login");
    exit;
}

$userid = $_SESSION["userid"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventid = isset($_POST["eventid"]) ? (int) $_POST["eventid"] : 0;

    if (isset($_POST["price"], $_POST["eventid"])) {
        $price = (float) $_POST["price"];

        createEventSubscription($userid, $eventid, $price);
        $xp = getEventXp($eventid);
        addUserXp($userid, $xp);

        header("Location: index.php?page=events");
        exit;
    }

    if (isset($_POST["eventid"])) {
        $event = getEventSubscriptionInfo($eventid);

        if (empty($event)) {
            header("Location: index.php");
            exit;
        }

        $title = $event["nom_evenement"];
        $xp = $event["xp_evenement"];
        $price = $event["prix_evenement"];

        $isDiscounted = (bool) $event["reductions_evenement"];
        $user_reduction = $isDiscounted ? getUserReductionRate($userid) : 1;
    } else {
        header("Location: index.php?page=login");
        exit;
    }
} else {
    header("Location: index.php?page=login");
    exit;
}

require_once 'app/views/event_subscription.php';
