<?php
require_once 'app/models/Database.php';

function getArticle($cart) {
    $db = new Database();
    $product_ids = array_keys($cart);
    $placeholders = implode(",", array_fill(0, count($product_ids), "?"));
    $query = "SELECT * FROM ARTICLE WHERE id_article IN ($placeholders)";
    $types = str_repeat("i", count($product_ids));
    return $db->select($query, $types, $product_ids);
}

function purchaseItem($userid, $product_id, $quantite, $mode_paiement) {
    $db = new Database();
    return $db->query(
        "CALL achat_article(?, ?, ?, ?)",
        "iiis",
        [$userid, $product_id, $quantite, $mode_paiement]
    );
}

function getReduction() {
    $db = new Database();
    return $db->query(
        "SELECT * FROM ADHESION 
        INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade 
        WHERE ADHESION.id_membre = ? AND reduction_grade > 0",
        "i",
        [$_SESSION['userid']]
    );
}