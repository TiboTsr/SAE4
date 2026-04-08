<?php
session_start();

require_once 'DB.php';
require_once 'tools.php';
require_once 'filter.php';

ini_set('display_errors', 0);
header('Content-Type: application/json');

if (!isset($_SESSION['userid']) || !tools::isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

ensureChatTableExists();

switch ($method) {
    case 'GET':
        get_messages();
        break;
    case 'POST':
        if (tools::methodAccepted('application/json')) {
            send_message();
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function ensureChatTableExists(): void
{
    $db = new DB();
    $db->query(
        "CREATE TABLE IF NOT EXISTS ADMIN_CHAT (
            id_message INT AUTO_INCREMENT PRIMARY KEY,
            id_membre INT NOT NULL,
            contenu_message VARCHAR(1000) NOT NULL,
            date_message DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_membre) REFERENCES MEMBRE(id_membre)
        )"
    );
}

function get_messages(): void
{
    $db = new DB();

    $limit = isset($_GET['limit'])
        ? filter::int($_GET['limit'], 1, 200)
        : 80;

    $rows = $db->select(
        "SELECT
            C.id_message,
            C.id_membre,
            C.contenu_message,
            C.date_message,
            M.prenom_membre,
            M.nom_membre
         FROM ADMIN_CHAT C
         INNER JOIN MEMBRE M ON M.id_membre = C.id_membre
         ORDER BY C.date_message DESC
         LIMIT ?",
        'i',
        [$limit]
    );

    $rows = array_reverse($rows);
    echo json_encode($rows);
}

function send_message(): void
{
    if (!isset($_SESSION['userid'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload) || !isset($payload['message'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing message']);
        return;
    }

    $raw = trim((string) $payload['message']);
    $message = filter::string($raw, minLenght: 1, maxLenght: 1000);

    $db = new DB();
    $db->query(
        "INSERT INTO ADMIN_CHAT (id_membre, contenu_message, date_message) VALUES (?, ?, NOW())",
        'is',
        [$_SESSION['userid'], $message]
    );

    echo json_encode(['message' => 'Message sent']);
}
