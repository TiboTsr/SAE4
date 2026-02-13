<?php
require_once 'app/models/Database.php';

function getEventsToDisplay($sql_date) {
    $db = new Database();

    return $db->select("SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement >= ? ORDER BY date_evenement ASC LIMIT 2;",
    "s",
    [$sql_date]
    );
}


function getAllEventsToDisplay($sql_date) {
    $db = new Database();

    return $db->select("SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement >= ? AND deleted = false ORDER BY date_evenement ASC;",
    "s",
    [$sql_date]
    );
}



function getPassedEventsToDisplay($sql_date, $show) {
    $db = new Database();

    return $db->select(
    "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement < ? AND deleted = false ORDER BY date_evenement ASC LIMIT ?;",
    "si",
    [$sql_date, $show]
);

}

function isPlaceAvailable($eventId) {
    $db = new Database();
    $result = $db->select(
        "SELECT (EVENEMENT.places_evenement - (SELECT COUNT(*) FROM INSCRIPTION WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement)) > 0 AS isPlaceDisponible 
        FROM EVENEMENT 
        WHERE EVENEMENT.id_evenement = ?;",
        "i",
        [$eventId]
    );
    return $result[0]['isPlaceDisponible'];
}

function isUserSubscribed($userId, $eventId) {
    $db = new Database();
    $result = $db->select(
        "SELECT MEMBRE.id_membre 
         FROM MEMBRE 
         JOIN INSCRIPTION ON MEMBRE.id_membre = INSCRIPTION.id_membre 
         WHERE MEMBRE.id_membre = ? AND INSCRIPTION.id_evenement = ?;",
        "ii",
        [$userId, $eventId]
    );
    return !empty($result);
}

function getTitle($eventid) {
    $db = new Database();
    return $db->select(
    "SELECT `nom_evenement` FROM EVENEMENT WHERE id_evenement = ?",
    "i",
    [$eventid]
    )[0];
}



function getEvent($eventid) {
    $db = new Database();
    return $db->select(
        "SELECT `nom_evenement`, `xp_evenement`, `places_evenement`, `prix_evenement`, `reductions_evenement`, `lieu_evenement`, `date_evenement`, `image_evenement`, `description_evenement`
        FROM EVENEMENT WHERE id_evenement = ?",
        "i",
        [$eventid]
    );
}


function getInscription($id, $userid) {
    $db = new Database();
    return $db->select(
        "SELECT * FROM INSCRIPTION WHERE id_evenement = ? AND id_membre = ?;",
        "ii",
        [$id, $userid]
    );
}


function insertSubscription($userid, $eventid, $price) {
    $db = new Database();
    $db->query(
        "INSERT INTO `INSCRIPTION` (`id_membre`, `id_evenement`, `date_inscription`, `paiement_inscription`, `prix_inscription`)
        VALUES (?, ?, NOW(), 'WEB', ?);",
        "iid",
        [$userid, $eventid, $price]
    );
}

function selectXpEvent($eventid) {
    $db = new Database();
    return $db->select(
        "SELECT xp_evenement FROM EVENEMENT WHERE id_evenement = ?", 
        "i", 
        [$eventid]
    )[0]['xp_evenement'];
    
}


function updateXp($xp, $userid) {
    $db = new Database();
    $db->query(
        "UPDATE MEMBRE SET MEMBRE.xp_membre = MEMBRE.xp_membre + ? where MEMBRE.id_membre = ?;",
        "ii",
        [$xp, $userid]
    );
}