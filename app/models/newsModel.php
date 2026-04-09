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

function getAdjacentNewsIds($newsId, $newsDate) {
    $db = new DB();

    $older = $db->select(
        "SELECT id_actualite
        FROM ACTUALITE
        WHERE date_actualite <= NOW()
          AND (date_actualite < ? OR (date_actualite = ? AND id_actualite < ?))
        ORDER BY date_actualite DESC, id_actualite DESC
        LIMIT 1",
        "ssi",
        [$newsDate, $newsDate, $newsId]
    );

    $newer = $db->select(
        "SELECT id_actualite
        FROM ACTUALITE
        WHERE date_actualite <= NOW()
          AND (date_actualite > ? OR (date_actualite = ? AND id_actualite > ?))
        ORDER BY date_actualite ASC, id_actualite ASC
        LIMIT 1",
        "ssi",
        [$newsDate, $newsDate, $newsId]
    );

    return [
        'older' => !empty($older) ? (int) $older[0]['id_actualite'] : null,
        'newer' => !empty($newer) ? (int) $newer[0]['id_actualite'] : null,
    ];
}
