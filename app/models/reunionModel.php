<?php
require_once 'core/DB.php';

function getAllReunions() {
    $db = new DB();
    $query = "SELECT id_reunion, date_reunion, fichier_reunion, id_membre
              FROM REUNION
              ORDER BY date_reunion DESC";
    return $db->select($query);
}

function getReunionById($id) {
    $db = new DB();
    $query = "SELECT id_reunion, date_reunion, fichier_reunion, id_membre
              FROM REUNION
              WHERE id_reunion = ?";
    return $db->select($query, "i", [$id]);
}
