<?php
require_once 'core/DB.php';

function getNews($show) {
    $db = new DB();
    return $db->select(
        "SELECT id_actualite, titre_actualite, date_actualite FROM ACTUALITE WHERE date_actualite <= NOW() ORDER BY date_actualite DESC LIMIT ?;",
        "i",
        [$show]
    );
}


function getNew($eventid) {
    $db = new DB();
    return $db->select(
        "SELECT *
        FROM ACTUALITE WHERE id_actualite = ?",
        "i",
        args: [$eventid]
    );
}
