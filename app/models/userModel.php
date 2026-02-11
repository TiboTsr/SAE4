<?php
require_once 'app/models/Database.php';

function getUserInfo() {
    $db = new Database();
    return $db->select("SELECT pp_membre, xp_membre, prenom_membre, nom_membre, email_membre, tp_membre, discord_token_membre, nom_grade, image_grade FROM MEMBRE LEFT JOIN ADHESION ON MEMBRE.id_membre = ADHESION.id_membre LEFT JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade WHERE MEMBRE.id_membre = ?;",
    "i",
    [$_SESSION['userid']]
    );
}


function updateUserPp($fileName) {
    $db = new Database();
    $db->query(
        "UPDATE MEMBRE SET pp_membre = ? WHERE id_membre = ?",
        "si",
        [$fileName, $_SESSION['userid']]
    );
}

function getMinimalUserInfo() {
    $db = new Database();
    return $db->select(
        "SELECT prenom_membre, nom_membre, email_membre, tp_membre FROM MEMBRE WHERE id_membre = ?",
        "i",
        [$_SESSION['userid']]
    );
}


function isEmail($mail) {
    $db = new Database();
    return $db->select(
        "SELECT id_membre FROM MEMBRE WHERE email_membre = ? AND id_membre != ?",
        "si",
        [$mail, $_SESSION['userid']]
    );
}

function updateUser($name, $lastName, $mail, $tp) {
    $db = new Database();
    $db->query(
        "UPDATE MEMBRE SET prenom_membre = ?, nom_membre = ?, email_membre = ?, tp_membre = ? WHERE id_membre = ?",
        "ssssi",
        [$name, $lastName, $mail, $tp, $_SESSION['userid']]
    );
}

function getPassword() {
    $db = new Database();
    return $db->select(
        "SELECT password_membre FROM MEMBRE WHERE id_membre = ?",
        "i",
        [$_SESSION['userid']]
    );
}

function updatePassword($hashedPassword) {
    $db = new Database();
    $db->query(
        "UPDATE MEMBRE SET password_membre = ? WHERE id_membre = ?",
        "si",
        [$hashedPassword, $_SESSION['userid']]
    );
}