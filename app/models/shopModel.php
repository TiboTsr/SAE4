<?php
require_once 'core/DB.php';

function getArticle($cart) {
    $db = new DB();
    $product_ids = array_keys($cart);
    $placeholders = implode(",", array_fill(0, count($product_ids), "?"));
    $query = "SELECT * FROM ARTICLE WHERE id_article IN ($placeholders)";
    $types = str_repeat("i", count($product_ids));
    return $db->select($query, $types, $product_ids);
}

function purchaseItem($userid, $product_id, $quantite, $mode_paiement) {
    $db = new DB();
    return $db->query(
        "CALL achat_article(?, ?, ?, ?)",
        "iiis",
        [$userid, $product_id, $quantite, $mode_paiement]
    );
}

function getReduction($userId) {
    $db = new DB();
    return $db->select(
        "SELECT * FROM ADHESION 
        INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade 
        WHERE ADHESION.id_membre = ? AND reduction_grade > 0",
        "i",
        [$userId]
    );
}


function getFilteredProducts($searchTerm, $filters, $orderBy) {
    $db = new DB();

    $query = "SELECT * FROM ARTICLE";
    $whereClauses = ["deleted = false"];
    $params = [];

    if (!empty($searchTerm)) {
        $whereClauses[] = "nom_article LIKE ?";
        $params[] = '%' . $searchTerm . '%';
    }

    if (!empty($filters)) {
        $placeholders = implode(", ", array_fill(0, count($filters), "?"));
        $whereClauses[] = "categorie_article IN ($placeholders)";
        $params = array_merge($params, $filters);
    }

    $query .= " WHERE " . implode(" AND ", $whereClauses);

    $orderMap = [
        'price_asc'  => 'prix_article ASC',
        'price_desc' => 'prix_article DESC',
        'name_asc'   => 'nom_article ASC',
        'name_desc'  => 'nom_article DESC',
    ];

    if (isset($orderMap[$orderBy])) {
        $query .= " ORDER BY " . $orderMap[$orderBy];
    }

    $types = str_repeat("s", count($params));
    return $db->select($query, $types ?: null, $params ?: null);
}
