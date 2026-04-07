<?php
session_start();

require_once 'DB.php';
require_once 'tools.php';


ini_set('display_errors', 0);

header('Content-Type: application/json');

tools::checkPermission('p_achat');

$DB = new DB();

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':                      # READ
        get_purchase();
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
                H.type_transaction,
                H.element,
                H.quantite,
                H.date_transaction,
                H.mode_paiement,
                H.montant,
                H.nom_utilisateur AS nom_membre,
                H.prenom_membre
            FROM HISTORIQUE AS H
            ORDER BY H.date_transaction DESC
        ");

        echo json_encode($data);
    } catch (\Throwable $error) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load purchases history']);
    }
}

