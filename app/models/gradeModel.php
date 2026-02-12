<?php
require_once 'app/models/Database.php';

function getGrade() {
    $db = new Database();
    return $db->select("SELECT * FROM GRADE WHERE deleted = false ORDER BY prix_grade");
}


function getGradeById($id_grade) {
    $db = new Database();
    return $db->select(
    "SELECT * FROM GRADE WHERE id_grade = ?",
    "i",
    [$id_grade]
    );
}


function getAdhesion($userid) {
    $db = new Database();
    return $db->select(
    "SELECT * FROM ADHESION WHERE id_membre = ?",
    "i",
    args: [$userid]
    );
}


function deleteAdhesion($userid) {
    $db = new Database();
    return $db->query(
    "DELETE FROM ADHESION WHERE id_membre = ?",
    "i",
    [$userid]
    );
}

function insertAdhesion($userid, $id_grade, $prix, $mode_paiement) {
    $db = new Database();
    return $db->query(
    "INSERT INTO ADHESION (id_membre, id_grade, prix_adhesion, paiement_adhesion, date_adhesion) VALUES (?, ?, ?, ?, NOW())",
    "iiss",
    [$userid, $id_grade, $prix, $mode_paiement]
    );
}