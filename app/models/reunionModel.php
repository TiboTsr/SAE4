<?php
require_once 'app/models/Database.php';

function getAllReunions() {
    $db = new Database();
    $query = "SELECT id_reunion, date_reunion, fichier_reunion, id_membre
              FROM reunion
              ORDER BY date_reunion DESC";
    return $db->select($query);
}

function getReunionById($id) {
    $db = new Database();
    $query = "SELECT id_reunion, date_reunion, fichier_reunion, id_membre
              FROM reunion
              WHERE id_reunion = ?";
    return $db->select($query, "i", [$id]);
}
