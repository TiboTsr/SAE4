<?php
require_once 'app/models/Database.php';

function eventColumnExists(string $columnName): bool {
    static $cache = [];

    if (array_key_exists($columnName, $cache)) {
        return $cache[$columnName];
    }

    $db = new Database();
    $result = $db->select(
        "SELECT 1
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'EVENEMENT'
           AND COLUMN_NAME = ?
         LIMIT 1",
        "s",
        [$columnName]
    );
    $cache[$columnName] = !empty($result);

    return $cache[$columnName];
}

function eventTypeSelectExpr(): string
{
    return eventColumnExists('type_evenement')
        ? "COALESCE(NULLIF(TRIM(`type_evenement`), ''), 'autre') AS `type_evenement`"
        : "'autre' AS `type_evenement`";
}

function getEventsToDisplay($sql_date) {
    $db = new Database();
    $typeSelect = eventTypeSelectExpr();

    return $db->select("SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement, {$typeSelect} FROM EVENEMENT WHERE date_evenement >= ? AND deleted = false ORDER BY date_evenement ASC LIMIT 2;",
    "s",
    [$sql_date]
    );
}


function getAllEventsToDisplay($sql_date) {
    $db = new Database();
    $typeSelect = eventTypeSelectExpr();

    return $db->select("SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement, {$typeSelect} FROM EVENEMENT WHERE date_evenement >= ? AND deleted = false ORDER BY date_evenement ASC;",
    "s",
    [$sql_date]
    );
}

function getEventsByDateDesc(int $show): array
{
    $db = new Database();
    $typeSelect = eventTypeSelectExpr();

    return $db->select(
        "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement, {$typeSelect}
         FROM EVENEMENT
         WHERE deleted = false
         ORDER BY date_evenement DESC
         LIMIT ?",
        "i",
        [$show]
    );
}



function getPassedEventsToDisplay($sql_date, $show) {
    $db = new Database();
    $typeSelect = eventTypeSelectExpr();

    return $db->select(
    "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement, {$typeSelect} FROM EVENEMENT WHERE date_evenement < ? AND deleted = false ORDER BY date_evenement ASC LIMIT ?;",
    "si",
    [$sql_date, $show]
);

}

function isPlaceAvailable($eventId) {
    $db = new Database();
    $result = $db->select(
        "SELECT
            CASE
                WHEN EVENEMENT.places_evenement = -1 THEN 1
                WHEN (EVENEMENT.places_evenement - (
                    SELECT COUNT(*)
                    FROM INSCRIPTION
                    WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement
                )) > 0 THEN 1
                ELSE 0
            END AS isPlaceDisponible
        FROM EVENEMENT 
        WHERE EVENEMENT.id_evenement = ? AND EVENEMENT.deleted = false;",
        "i",
        [$eventId]
    );
    return !empty($result) ? $result[0]['isPlaceDisponible'] : false;
}

function isUnlimitedEvent($eventId) {
    $db = new Database();
    $result = $db->select(
        "SELECT places_evenement = -1 AS isUnlimited
         FROM EVENEMENT
         WHERE id_evenement = ? AND deleted = false",
        "i",
        [$eventId]
    );

    return !empty($result) ? (bool) $result[0]['isUnlimited'] : false;
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
    "SELECT `nom_evenement` FROM EVENEMENT WHERE id_evenement = ? AND deleted = false",
    "i",
    [$eventid]
    )[0];
}



function getEvent($eventid) {
    $db = new Database();
    $imageSelect = eventColumnExists('image_evenement') ? "`image_evenement`" : "NULL AS `image_evenement`";
    $descriptionSelect = eventColumnExists('description_evenement')
        ? "COALESCE(`description_evenement`, '') AS `description_evenement`"
        : "'' AS `description_evenement`";
    $typeSelect = eventTypeSelectExpr();

    return $db->select(
        "SELECT `nom_evenement`, `xp_evenement`, `places_evenement`, `prix_evenement`, `reductions_evenement`, `lieu_evenement`, `date_evenement`,
                {$imageSelect}, {$descriptionSelect}, {$typeSelect}
        FROM EVENEMENT WHERE id_evenement = ? AND deleted = false",
        "i",
        [$eventid]
    );
}

function getRemainingPlaces(int $eventId): int {
    $db = new Database();
    $result = $db->select(
        "SELECT
            CASE
                WHEN places_evenement = -1 THEN -1
                ELSE places_evenement - (
                    SELECT COUNT(*)
                    FROM INSCRIPTION
                    WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement
                )
            END AS remaining_places
         FROM EVENEMENT
         WHERE id_evenement = ? AND deleted = false",
        "i",
        [$eventId]
    );

    return !empty($result) ? (int) ($result[0]['remaining_places'] ?? 0) : 0;
}


function getInscription($id, $userid) {
    $db = new Database();
    return $db->select(
        "SELECT * FROM INSCRIPTION WHERE id_evenement = ? AND id_membre = ?;",
        "ii",
        [$id, $userid]
    );
}


function insertSubscription($userid, $eventid, $price, $mode_paiement) {
    $db = new Database();
    $db->query(
        "INSERT INTO `INSCRIPTION` (`id_membre`, `id_evenement`, `date_inscription`, `paiement_inscription`, `prix_inscription`)
        VALUES (?, ?, NOW(), ?, ?);",
        "iisd",
        [$userid, $eventid, $mode_paiement, $price]
    );
}

function deleteSubscription($userid, $eventid) {
    $db = new Database();
    $db->query(
        "DELETE FROM INSCRIPTION WHERE id_membre = ? AND id_evenement = ?;",
        "ii",
        [$userid, $eventid]
    );
}

function selectXpEvent($eventid) {
    $db = new Database();
    return $db->select(
        "SELECT xp_evenement FROM EVENEMENT WHERE id_evenement = ? AND deleted = false", 
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

function removeXp($xp, $userid) {
    $db = new Database();
    $db->query(
        "UPDATE MEMBRE
         SET MEMBRE.xp_membre = GREATEST(0, MEMBRE.xp_membre - ?)
         WHERE MEMBRE.id_membre = ?;",
        "ii",
        [$xp, $userid]
    );
}

function getEventSubscriptionInfo($eventId) {
    $db = new Database();
    $result = $db->select(
        "SELECT nom_evenement, xp_evenement, prix_evenement, reductions_evenement
         FROM EVENEMENT
         WHERE id_evenement = ? AND deleted = false",
        "i",
        [$eventId]
    );

    return $result[0] ?? null;
}

function getSubscribedEventsByUser(int $userId): array
{
    $db = new Database();

    return $db->select(
        "SELECT 
            e.id_evenement,
            e.nom_evenement,
            e.description_evenement,
            e.lieu_evenement,
            e.date_evenement,
            e.prix_evenement,
            e.reductions_evenement,
            e.xp_evenement
         FROM EVENEMENT e
         INNER JOIN INSCRIPTION i ON i.id_evenement = e.id_evenement
         WHERE i.id_membre = ? 
           AND e.deleted = false
         ORDER BY e.date_evenement ASC",
        "i",
        [$userId]
    );
}

function getUserReductionRate($userId) {
    $db = new Database();
    $result = $db->select(
        "SELECT reduction_grade
         FROM ADHESION
         JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
         WHERE id_membre = ? AND reduction_grade > 0
         ORDER BY ADHESION.date_adhesion DESC
         LIMIT 1",
        "i",
        [$userId]
    );

    if (empty($result)) {
        return 1.0;
    }

    return 1 - ((float) $result[0]['reduction_grade'] / 100);
}

function createEventSubscription($userId, $eventId, $price, $mode_paiement) {
    if (isUnlimitedEvent($eventId)) {
        $db = new Database();
        try {
            $db->query("UPDATE EVENEMENT SET places_evenement = 2147483647 WHERE id_evenement = ?", "i", [$eventId]);
            insertSubscription($userId, $eventId, $price, $mode_paiement);
            updateXp(getEventXp($eventId), $userId);
        } finally {
            $db->query("UPDATE EVENEMENT SET places_evenement = -1 WHERE id_evenement = ?", "i", [$eventId]);
        }

        return;
    }

    insertSubscription($userId, $eventId, $price, $mode_paiement);
    updateXp(getEventXp($eventId), $userId);
}

function getEventXp($eventId) {
    return (int) selectXpEvent($eventId);
}

function addUserXp($userId, $xp) {
    updateXp($xp, $userId);
}

function cancelEventSubscription($userId, $eventId) {
    deleteSubscription($userId, $eventId);
    removeXp(getEventXp($eventId), $userId);
}

function removeUserXp($userId, $xp) {
    removeXp($xp, $userId);
}
