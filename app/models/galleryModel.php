<?php
require_once 'app/models/Database.php';

function getMedias($userid, $eventid, $limit) {
    $db = new Database();
    return $db->select(
    "SELECT id_media, url_media FROM `MEDIA` WHERE id_membre = ? and id_evenement = ? ORDER by date_media ASC LIMIT ?;",
    "iii",
    [$userid, $eventid, $limit]
    );
}

function addMedia($fileName, $sqlDate, $userid, $eventid) {
    $db = new Database();
    $db->query(
        "INSERT INTO MEDIA VALUES (NULL, ?, ?, ?, ?);",
        "ssii",
        [$fileName, $sqlDate, $userid, $eventid]
    );
}

function getMedia($mediaid, $eventid) {
    $db = new Database();
    return $db->select(
        "SELECT url_media FROM `MEDIA` WHERE id_media = ? AND id_evenement = ?",
        "ii",
        [$mediaid, $eventid]
    )[0]['url_media'];
}

function deleteMedia($mediaid, $eventid) {
    $db = new Database();
    $db->query(
        "DELETE FROM MEDIA WHERE id_media = ? AND id_evenement = ?",
        "ii",
        [$mediaid, $eventid]
    );
}


function getMediaOrderBy($eventid, $show) {
    $db = new Database();
    return $db->select(
        "SELECT url_media FROM `MEDIA` WHERE id_evenement = ? ORDER by date_media ASC LIMIT ? ;",
        "ii",
        [$eventid, $show]
    );
}

function getMediaOrderByWithLimit($eventid, $show) {
    $db = new Database();
    return $db->select(
        "SELECT url_media FROM `MEDIA` WHERE id_evenement = ? ORDER by date_media ASC LIMIT ? ;",
        "ii",
        [$eventid, $show]
    );
}