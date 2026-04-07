<?php

require_once 'app/models/eventsModel.php';
require_once 'app/models/userModel.php';

$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: index.php?page=login");
    exit;
}

$userid = $_SESSION["userid"];
$userInfo = getMinimalUserInfo();
$checkoutProfile = [
    'prenom' => '',
    'nom' => '',
    'email' => '',
    'tp' => '',
];

if (!empty($userInfo)) {
    $checkoutProfile['prenom'] = (string) ($userInfo[0]['prenom_membre'] ?? '');
    $checkoutProfile['nom'] = (string) ($userInfo[0]['nom_membre'] ?? '');
    $checkoutProfile['email'] = (string) ($userInfo[0]['email_membre'] ?? '');
    $checkoutProfile['tp'] = (string) ($userInfo[0]['tp_membre'] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventid = isset($_POST["eventid"]) ? (int) $_POST["eventid"] : 0;
    $action = $_POST['action'] ?? '';

    if ($action === 'unsubscribe' && $eventid > 0) {
        if (isUserSubscribed($userid, $eventid)) {
            cancelEventSubscription($userid, $eventid);
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
        $mode_paiement = $_POST['mode_paiement'] ?? '';

        if ($price <= 0) {
            $mode_paiement = 'gratuit';
        } else {
            $allowedPaymentModes = ['carte_credit', 'paypal', 'Especes', 'especes'];
            if (!in_array($mode_paiement, $allowedPaymentModes, true)) {
                $_SESSION['message'] = "Mode de paiement invalide.";
                $_SESSION['message_type'] = 'error';
                header("Location: index.php?page=event_details&id=" . $eventid);
                exit;
            }

            if (strtolower($mode_paiement) === 'especes') {
                $mode_paiement = 'Especes';
            }
        }

        if (isUserSubscribed($userid, $eventid)) {
            $_SESSION['message'] = "Vous etes deja inscrit a cet evenement.";
            $_SESSION['message_type'] = 'error';
        } else {
            try {
                createEventSubscription($userid, $eventid, $price, $mode_paiement);
                $_SESSION['message'] = "Inscription confirmee.";
                $_SESSION['message_type'] = 'success';
            } catch (\Throwable $e) {
                $_SESSION['message'] = "Inscription impossible pour le moment.";
                $_SESSION['message_type'] = 'error';
            }
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
        $remainingPlaces = getRemainingPlaces($eventid);
    } else {
        header("Location: index.php?page=login");
        exit;
    }
} else {
    header("Location: index.php?page=login");
    exit;
}

require_once 'app/views/event_subscription.php';
