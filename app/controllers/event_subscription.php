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
    $action = $_POST['action'] ?? '';

    if ($action === 'unsubscribe' && $eventid > 0) {
        if (isUserSubscribed($userid, $eventid)) {
            cancelEventSubscription($userid, $eventid);
            $xp = getEventXp($eventid);
            removeUserXp($userid, $xp);
            $_SESSION['message'] = "Desinscription reussie.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Vous n'etes pas inscrit a cet evenement.";
            $_SESSION['message_type'] = 'error';
        }

        header("Location: index.php?page=event_details&id=" . $eventid);
        exit;
    }

    if (isset($_POST["price"], $_POST["eventid"])) {
        $price = (float) $_POST["price"];

        if (isUserSubscribed($userid, $eventid)) {
            $_SESSION['message'] = "Vous etes deja inscrit a cet evenement.";
            $_SESSION['message_type'] = 'error';
        } else {
            createEventSubscription($userid, $eventid, $price);
            $xp = getEventXp($eventid);
            addUserXp($userid, $xp);
            $_SESSION['message'] = "Inscription confirmee.";
            $_SESSION['message_type'] = 'success';
        }

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
