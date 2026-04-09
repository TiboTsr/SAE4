<?php
require_once 'core/DB.php';
require_once 'app/models/userModel.php';
require_once 'app/models/files_save.php';

$isLoggedIn = isset($_SESSION['userid']);

// Déconnexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deconnexion']) && $_POST['deconnexion'] === 'true') {
    session_destroy();
    header("Location: index.php");
    exit();
}

$infoUser = getUserInfo();

// Modification de la photo de profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = saveImage();

    if ($fileName !== null) {
        if (!empty($infoUser[0]['pp_membre'])) {
            deleteFile($infoUser[0]['pp_membre']);
        }
        updateUserPp($fileName);
        $_SESSION['message']      = "Mise à jour de la photo de profil réussie !";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message']      = "Erreur : veuillez vérifier le fichier envoyé.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: index.php?page=account");
    exit();
}

// Modification des informations personnelles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['lastName'], $_POST['mail'])) {
    $currentUserData = getMinimalUserInfo();

    if (!empty($currentUserData)) {
        $name     = empty($_POST['name'])     ? $currentUserData[0]['prenom_membre'] : htmlspecialchars($_POST['name']);
        $lastName = empty($_POST['lastName']) ? $currentUserData[0]['nom_membre']    : htmlspecialchars($_POST['lastName']);
        $mail     = empty($_POST['mail'])     ? $currentUserData[0]['email_membre']  : htmlspecialchars($_POST['mail']);
        $tp       = isset($_POST['tp']) && !empty($_POST['tp']) ? htmlspecialchars($_POST['tp']) : $currentUserData[0]['tp_membre'];

        $existingEmail = isEmail($mail);

        if (!empty($existingEmail)) {
            $_SESSION['message']      = "Les modifications n'ont pas pu être effectuées car l'adresse e-mail est déjà utilisée par un autre compte.";
            $_SESSION['message_type'] = "error";
        } else {
            updateUser($name, $lastName, $mail, $tp);
            $_SESSION['message']      = "Vos informations ont été mises à jour avec succès !";
            $_SESSION['message_type'] = "success";
        }
    } else {
        $_SESSION['message']      = "Erreur : utilisateur introuvable dans la base de données.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: index.php?page=account");
    exit();
}

// Modification du mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mdp'], $_POST['newMdp'], $_POST['newMdpVerif'])) {
    $currentPassword  = trim($_POST['mdp']);
    $newPassword      = trim($_POST['newMdp']);
    $newPasswordVerif = trim($_POST['newMdpVerif']);

    $user = getPassword();

    if (!empty($user)) {
        $password_ok = ($user[0]['password_membre'] === null && $currentPassword === '')
            ? true
            : password_verify($currentPassword, $user[0]['password_membre']);

        if (!$password_ok) {
            $_SESSION['message']      = "Mot de passe actuel incorrect.";
            $_SESSION['message_type'] = "error";
        } elseif ($newPassword !== $newPasswordVerif) {
            $_SESSION['message']      = "Les nouveaux mots de passe ne correspondent pas.";
            $_SESSION['message_type'] = "error";
        } else {
            updatePassword(password_hash($newPassword, PASSWORD_DEFAULT));
            $_SESSION['message']      = "Mot de passe mis à jour avec succès !";
            $_SESSION['message_type'] = "success";
        }
    } else {
        $_SESSION['message']      = "Erreur : utilisateur introuvable dans la base de données.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: index.php?page=account");
    exit();
}

// Préparation des données pour la vue
$viewAll = isset($_GET['viewAll']) && $_GET['viewAll'] === '1';
$historiqueAchats = getHistoriqueAchats($_SESSION['userid'], $viewAll);

require_once 'app/views/account.php';
