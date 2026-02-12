<?php
require_once 'app/models/Database.php';

function getNews($show) {
    $db = new Database();
    return $db->select(
        "SELECT id_actualite, titre_actualite, date_actualite FROM ACTUALITE WHERE date_actualite <= NOW() ORDER BY date_actualite ASC LIMIT ?;",
        "i",
        [$show]
    );
}


function getNew($eventid) {
    $db = new Database();
    return $db->select(
        "SELECT *
        FROM ACTUALITE WHERE id_actualite = ?",
        "i",
        args: [$eventid]
    );
}