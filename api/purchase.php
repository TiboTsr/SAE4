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
    case 'POST':                     # CREATE
        if (tools::methodAccepted('application/json')) {
            create_purchase();
        }
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

    if (isset($_GET['meta']) && filter::bool($_GET['meta']) === true) {
        get_purchase_meta($db);
        return;
    }

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
                CASE
                    WHEN M.id_membre IS NULL OR (M.nom_membre = 'N/A' AND M.prenom_membre = 'N/A')
                        THEN 'Client non inscrit'
                    ELSE M.nom_membre
                END AS nom_membre,
                CASE
                    WHEN M.id_membre IS NULL OR (M.nom_membre = 'N/A' AND M.prenom_membre = 'N/A')
                        THEN ''
                    ELSE M.prenom_membre
                END AS prenom_membre
            FROM COMMANDE C
            INNER JOIN ARTICLE A ON A.id_article = C.id_article
            LEFT JOIN MEMBRE M ON M.id_membre = C.id_membre

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

function get_purchase_meta(DB $db): void {
    try {
        $hasDeletedColumnRows = $db->select(
            "SELECT COUNT(*) AS count_deleted
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'ARTICLE'
               AND COLUMN_NAME = 'deleted'"
        );
        $hasDeletedColumn = ((int)($hasDeletedColumnRows[0]['count_deleted'] ?? 0)) > 0;

        $articlesQuery = "
            SELECT
                id_article,
                nom_article,
                prix_article,
                stock_article,
                reduction_article
            FROM ARTICLE
        ";
        if ($hasDeletedColumn) {
            $articlesQuery .= " WHERE deleted = FALSE";
        }
        $articlesQuery .= " ORDER BY nom_article ASC";

        $articles = $db->select($articlesQuery);

        $users = $db->select("
            SELECT
                id_membre,
                nom_membre,
                prenom_membre
            FROM MEMBRE
            WHERE NOT (nom_membre = 'N/A' AND prenom_membre = 'N/A')
            ORDER BY nom_membre ASC, prenom_membre ASC
        ");

        echo json_encode([
            'articles' => $articles,
            'users' => $users,
        ]);
    } catch (\Throwable $error) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load purchases metadata']);
    }
}

function create_purchase() : void {
    $payload = json_decode(file_get_contents('php://input'), true);

    if (!is_array($payload) || !isset($payload['id_article'], $payload['quantite'], $payload['mode_paiement'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $idArticle = filter::int($payload['id_article'], min: 1);
    $quantite = filter::int($payload['quantite'], min: 1);
    $modePaiement = normalize_payment_mode(filter::string($payload['mode_paiement'], maxLenght: 50));
    $recupere = isset($payload['recupere']) ? (filter::bool($payload['recupere']) ? 1 : 0) : 0;

    $idMembre = null;
    if (array_key_exists('id_membre', $payload) && $payload['id_membre'] !== null && $payload['id_membre'] !== '') {
        $idMembre = filter::int($payload['id_membre']);
    }

    $db = new DB();

    try {
        $hasDeletedColumnRows = $db->select(
            "SELECT COUNT(*) AS count_deleted
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'ARTICLE'
               AND COLUMN_NAME = 'deleted'"
        );
        $hasDeletedColumn = ((int)($hasDeletedColumnRows[0]['count_deleted'] ?? 0)) > 0;

        $articleQuery = "
            SELECT id_article, prix_article, stock_article, xp_article, reduction_article
            FROM ARTICLE
            WHERE id_article = ?
        ";
        if ($hasDeletedColumn) {
            $articleQuery .= " AND deleted = FALSE";
        }

        $articleRows = $db->select($articleQuery, "i", [$idArticle]);
        if (count($articleRows) === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found']);
            return;
        }

        $article = $articleRows[0];

        if ((int)$article['stock_article'] >= 0 && $quantite > (int)$article['stock_article']) {
            http_response_code(409);
            echo json_encode(['error' => 'Not enough stock']);
            return;
        }

        if ($idMembre !== null) {
            $memberRows = $db->select("SELECT id_membre FROM MEMBRE WHERE id_membre = ?", "i", [$idMembre]);
            if (count($memberRows) === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }
        }

        $reductionFactor = 1.0;
        if ($idMembre !== null && (int)$article['reduction_article'] === 1) {
            $gradeRows = $db->select(
                "SELECT G.reduction_grade
                 FROM ADHESION A
                 INNER JOIN GRADE G ON G.id_grade = A.id_grade
                 WHERE A.id_membre = ?
                 ORDER BY A.date_adhesion DESC
                 LIMIT 1",
                "i",
                [$idMembre]
            );

            if (count($gradeRows) > 0) {
                $gradeReduction = (float)$gradeRows[0]['reduction_grade'];
                if ($gradeReduction > 0) {
                    $reductionFactor = max(0.0, 1 - ($gradeReduction / 100));
                }
            }
        }

        $prixCommande = round(((float)$article['prix_article'] * $quantite * $reductionFactor), 2);
        $isAnonymousOrder = ($idMembre === null);
        $effectiveMemberId = $idMembre;

        if ($isAnonymousOrder && !is_column_nullable($db, 'COMMANDE', 'id_membre')) {
            $effectiveMemberId = get_or_create_anonymous_member_id($db);
        }

        if ($effectiveMemberId === null) {
            $idCommande = $db->query(
                "INSERT INTO COMMANDE (statut_commande, prix_commande, paiement_commande, date_commande, qte_commande, id_membre, id_article)
                 VALUES (?, ?, ?, NOW(), ?, NULL, ?)",
                "idsii",
                [$recupere, $prixCommande, $modePaiement, $quantite, $idArticle]
            );
        } else {
            $idCommande = $db->query(
                "INSERT INTO COMMANDE (statut_commande, prix_commande, paiement_commande, date_commande, qte_commande, id_membre, id_article)
                 VALUES (?, ?, ?, NOW(), ?, ?, ?)",
                "idsiii",
                [$recupere, $prixCommande, $modePaiement, $quantite, $effectiveMemberId, $idArticle]
            );
        }

        if ((int)$article['stock_article'] >= 0) {
            $db->query(
                "UPDATE ARTICLE SET stock_article = stock_article - ? WHERE id_article = ?",
                "ii",
                [$quantite, $idArticle]
            );
        }

        if (!$isAnonymousOrder && $idMembre !== null) {
            $xpGagne = (int)$article['xp_article'] * $quantite;
            $db->query(
                "UPDATE MEMBRE SET xp_membre = xp_membre + ? WHERE id_membre = ?",
                "ii",
                [$xpGagne, $idMembre]
            );
        }

        http_response_code(201);
        echo json_encode([
            'message' => 'Purchase created',
            'id_commande' => $idCommande,
        ]);
    } catch (\Throwable $error) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create purchase']);
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

function normalize_payment_mode(string $mode): string
{
    $modeLower = strtolower(trim($mode));
    $map = [
        'especes' => 'Especes',
        'espèces' => 'Especes',
        'paypal' => 'PayPal',
        'carte_credit' => 'Carte de credit',
        'carte de credit' => 'Carte de credit',
        'cb' => 'Carte de credit',
        'tpe' => 'TPE',
    ];

    if (isset($map[$modeLower])) {
        return $map[$modeLower];
    }

    return $mode;
}

function is_column_nullable(DB $db, string $table, string $column): bool
{
    $columnRows = $db->select(
        "SELECT IS_NULLABLE
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = ?
           AND COLUMN_NAME = ?",
        "ss",
        [$table, $column]
    );

    return count($columnRows) > 0 && strtoupper((string)$columnRows[0]['IS_NULLABLE']) === 'YES';
}

function get_or_create_anonymous_member_id(DB $db): int
{
    $existing = $db->select(
        "SELECT id_membre
         FROM MEMBRE
         WHERE nom_membre = 'N/A' AND prenom_membre = 'N/A'
         ORDER BY id_membre ASC
         LIMIT 1"
    );

    if (count($existing) > 0) {
        return (int)$existing[0]['id_membre'];
    }

    $anonymousEmail = 'anonymous.order@bde.local';
    $anonymousPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

    return $db->query(
        "INSERT INTO MEMBRE (nom_membre, prenom_membre, email_membre, password_membre, xp_membre, discord_token_membre, pp_membre, tp_membre)
         VALUES ('N/A', 'N/A', ?, ?, 0, NULL, 'N/A', NULL)",
        "ss",
        [$anonymousEmail, $anonymousPassword]
    );
}

