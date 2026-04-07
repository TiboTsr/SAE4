<?php
session_start();

require_once 'DB.php';
require_once 'tools.php';
require_once 'filter.php';


ini_set('display_errors', 0);

header('Content-Type: application/json');

tools::checkPermission('p_achat');

$DB = new DB();

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':                      # READ
        get_purchase();
        break;
    case 'PATCH':                    # UPDATE (statut commande)
        if (tools::methodAccepted('application/json')) {
            update_purchase_status();
        }
        break;
    default:
        # 405 Method Not Allowed
        http_response_code(405);
        break;
}

function get_purchase() : void {
    $db = new DB();

    try {
        $data = $db->select("
            SELECT
                'Commande' AS type_transaction,
                C.id_commande,
                A.nom_article AS element,
                C.qte_commande AS quantite,
                C.date_commande AS date_transaction,
                C.paiement_commande AS mode_paiement,
                C.prix_commande AS montant,
                C.statut_commande AS recupere,
                M.nom_membre,
                M.prenom_membre
            FROM COMMANDE C
            INNER JOIN ARTICLE A ON A.id_article = C.id_article
            INNER JOIN MEMBRE M ON M.id_membre = C.id_membre

            UNION ALL

            SELECT
                'Inscription' AS type_transaction,
                NULL AS id_commande,
                E.nom_evenement AS element,
                1 AS quantite,
                I.date_inscription AS date_transaction,
                I.paiement_inscription AS mode_paiement,
                I.prix_inscription AS montant,
                1 AS recupere,
                M.nom_membre,
                M.prenom_membre
            FROM INSCRIPTION I
            INNER JOIN EVENEMENT E ON E.id_evenement = I.id_evenement
            INNER JOIN MEMBRE M ON M.id_membre = I.id_membre

            UNION ALL

            SELECT
                'Adhesion' AS type_transaction,
                NULL AS id_commande,
                G.nom_grade AS element,
                1 AS quantite,
                AD.date_adhesion AS date_transaction,
                AD.paiement_adhesion AS mode_paiement,
                AD.prix_adhesion AS montant,
                1 AS recupere,
                M.nom_membre,
                M.prenom_membre
            FROM ADHESION AD
            INNER JOIN GRADE G ON G.id_grade = AD.id_grade
            INNER JOIN MEMBRE M ON M.id_membre = AD.id_membre

            ORDER BY date_transaction DESC
        ");

        echo json_encode($data);
    } catch (\Throwable $error) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load purchases history']);
    }
}

function update_purchase_status() : void {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing command id']);
        return;
    }

    $id = filter::int($_GET['id']);
    $payload = json_decode(file_get_contents('php://input'), true);

    if (!is_array($payload) || !array_key_exists('recupere', $payload)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing recupere field']);
        return;
    }

    $recupere = filter::bool($payload['recupere']) ? 1 : 0;

    $db = new DB();
    $exists = $db->select("SELECT id_commande FROM COMMANDE WHERE id_commande = ?", "i", [$id]);
    if (count($exists) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Commande not found']);
        return;
    }

    $db->query("UPDATE COMMANDE SET statut_commande = ? WHERE id_commande = ?", "ii", [$recupere, $id]);
    echo json_encode(['message' => 'Status updated']);
}

