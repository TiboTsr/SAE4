<?php
session_start();
require_once 'DB.php';

ini_set('display_errors', 0);

header('Content-Type: application/json');

if (isset($_SESSION['userid'])) {

    $db = new \DB();

    $results = $db->select("SELECT * FROM LISTE_PERMISSIONS WHERE id_membre = ?", 'i', [$_SESSION['userid']]);


    if (count($results) > 0) {
        $permissions = $results[0];
        $permissions['id_membre'] = $_SESSION['userid'];
        echo json_encode($permissions);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Permissions not found"]);
    }

} else {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
}
