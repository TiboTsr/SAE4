
<?php 
require_once 'app/models/Database.php';
require_once 'app/models/shopModel.php';
require_once 'app/models/files_save.php';
require_once 'app/models/Cart.php';

// Initialisation du panier
$db = new Database();
$cart = new Cart($db);


// Gestion de la recherche, des filtres et tris

//Traitement du formulaire
$filters = [];
$orderBy = "name_asc";
$searchTerm = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        $filters = [];
        $orderBy = "name_asc";
        $searchTerm = "";
    } else {
        if (isset($_POST['category'])) {
            $filters = $_POST['category'];
        }
        if (isset($_POST['sort'])) {
            $orderBy = $_POST['sort'];
        }
        if (!empty($_POST['search'])) {
            $searchTerm = $_POST['search'];
        }
    }
}

//Construction de la requête SQL
$query = "SELECT * FROM ARTICLE";
$whereClauses = ["deleted = false"];
$params = [];
// Ajout de la recherche par nom
if (!empty($searchTerm)) {
    $whereClauses[] = "nom_article LIKE ?";
    $params[] = '%' . $searchTerm . '%';
}
// Ajout des filtres par catégorie
if (!empty($filters)) {
    $placeholders = implode(", ", array_fill(0, count($filters), "?"));
    $whereClauses[] = "categorie_article IN ($placeholders)";
    $params = array_merge($params, $filters);
}
// Ajout des clauses WHERE
if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}
// Ajout du tri
if ($orderBy === "price_asc") {
    $query .= " ORDER BY prix_article ASC";
} elseif ($orderBy === "price_desc") {
    $query .= " ORDER BY prix_article DESC";
} elseif ($orderBy === "name_asc") {
    $query .= " ORDER BY nom_article ASC";
} elseif ($orderBy === "name_desc") {
    $query .= " ORDER BY nom_article DESC";
}
// Exécution de la requête
$products = $db->select($query, str_repeat("s", count($params)), $params);


require_once 'app/views/shop.php';


