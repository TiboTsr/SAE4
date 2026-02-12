<?php

require_once 'app/models/gradeModel.php';
require_once 'app/models/files_save.php';

$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: index.php?page=login");
    exit;
}

$userid = $_SESSION["userid"];


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

// Vérifie si l'utilisateur possède déjà un grade
$currentGrade = getAdhesion($userid);

// Gestion de l'achat d'un grade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mode_paiement']) && !empty($_POST['mode_paiement'])) {
        $mode_paiement = $_POST['mode_paiement'];
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