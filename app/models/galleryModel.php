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
