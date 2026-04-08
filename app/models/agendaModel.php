<?php

require_once 'app/models/Database.php';
function getAgendaByUserId($userId) {
    $db = new Database();
    $query = "SELECT id_agenda, id_membre, url_edt, format_source, date_ajout, date_modification, actif
              FROM ADMIN_AGENDA
              WHERE id_membre = ? AND actif = TRUE";
    return $db->select($query, "i", [$userId]);
}

function insertAgendaUser($idMembre, $urlEdt, $formatSource = 'ics') {
    $db = new Database();
    $query = "INSERT INTO ADMIN_AGENDA (id_membre, url_edt, format_source, date_ajout, actif)
              VALUES (?, ?, ?, NOW(), TRUE)";
    return $db->query($query, "iss", [$idMembre, $urlEdt, $formatSource]);
}

function updateAgendaUtilisateur($idMembre, $urlEdt, $formatSource = 'ics') {
    $db = new Database();
    $query = "UPDATE ADMIN_AGENDA
              SET url_edt = ?, format_source = ?, date_modification = NOW()
              WHERE id_membre = ?";
    return $db->query($query, "ssi", [$urlEdt, $formatSource, $idMembre]);
}