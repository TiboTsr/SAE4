<?php

require_once 'app/models/gradeModel.php';
require_once 'app/models/files_save.php';
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

// Vérification que l'ID du grade est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?page=grade");
    exit;
}
$id_grade = intval($_GET['id']);

// On récupère les informations du grade
$grade = getGradeById($id_grade);
// Vérifie que le grade existe
if (empty($grade)) {
    $_SESSION['message'] = "Le grade sélectionné n'existe pas.";
    $_SESSION['message_type'] = "error";
    header("Location: index.php?page=grade");
    exit;
}

$grade = $grade[0];
$prix = (float) $grade['prix_grade'];
// Vérifie si l'utilisateur possède déjà un grade
$currentGrade = getAdhesion($userid);

// Gestion de l'achat d'un grade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mode_paiement']) && !empty($_POST['mode_paiement'])) {
        $mode_paiement = $_POST['mode_paiement'];
        $allowedPaymentModes = ['carte_credit', 'paypal', 'Especes', 'especes'];
        if (!in_array($mode_paiement, $allowedPaymentModes, true)) {
            $_SESSION['message'] = "Mode de paiement invalide.";
            $_SESSION['message_type'] = "error";
            header("Location: index.php?page=grade_subscription&id=$id_grade");
            exit;
        }

        if (strtolower($mode_paiement) === 'especes') {
            $mode_paiement = 'Especes';
        }

        if (!empty($currentGrade)) {
            deleteAdhesion($userid);
        }
        insertAdhesion($userid, $id_grade, $prix, $mode_paiement);

        $_SESSION['message'] = "Adhésion au grade réussie !";
        $_SESSION['message_type'] = "success";
        header("Location: index.php?page=grade");
        exit;
    }
}

require 'app/views/grade_subscription.php';
