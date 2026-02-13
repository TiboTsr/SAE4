<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adhérer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<<<<<<< HEAD:app/views/grade_subscription.php
    <link rel="stylesheet" href="assets/styles/grade_subscription_style.css">

    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">
=======
    <link rel="stylesheet" href="styles/grade_subscription_style.css">

    <link rel="stylesheet" href="styles/general_style.css">
    <link rel="stylesheet" href="styles/header_style.css">
    <link rel="stylesheet" href="styles/footer_style.css">
>>>>>>> Mouad:grade_subscription.php
</head>

<body class="body_margin">

<?php
<<<<<<< HEAD:app/views/grade_subscription.php
require_once "app/views/header.php";
=======

// Importer les fichiers
require_once "header.php";
require_once 'database.php';
require_once 'files_save.php';

// Connexion à la base de données
$db = new DB();

$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION["userid"];


// Vérification que l'ID du grade est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: grade.php");
    exit;
}
$id_grade = intval($_GET['id']);


// On récupère les informations du grade
$grade = $db->select(
    "SELECT * FROM GRADE WHERE id_grade = ?",
    "i",
    [$id_grade]
);

// Vérifie que le grade existe
if (empty($grade)) {
    $_SESSION['message'] = "Le grade sélectionné n'existe pas.";
    $_SESSION['message_type'] = "error";
    header("Location: grade.php");
    exit;
}

// Vérifie si l'utilisateur possède déjà un grade
$currentGrade = $db->select(
    "SELECT * FROM ADHESION WHERE id_membre = ?",
    "i",
    [$userid]
);

// Gestion de l'achat d'un grade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mode_paiement']) && !empty($_POST['mode_paiement'])) {
        $mode_paiement = $_POST['mode_paiement'];
        if (!empty($currentGrade)) {
            $db->query(
                "DELETE FROM ADHESION WHERE id_membre = ?",
                "i",
                [$userid]
            );
        }
        $db->query(
            "INSERT INTO ADHESION (id_membre, id_grade, prix_adhesion, paiement_adhesion, date_adhesion) VALUES (?, ?, ?, ?, NOW())",
            "iiss",
            [$userid, $id_grade, $grade[0]['prix_grade'], $mode_paiement]
        );

        $_SESSION['message'] = "Adhésion au grade réussie !";
        $_SESSION['message_type'] = "success";
        header("Location: grade.php");
        exit;
    } else {
    }
}
>>>>>>> Mouad:grade_subscription.php
?>

<h1>MON ADHESION</h1>

<div>
    <button id="cart-button">
<<<<<<< HEAD:app/views/grade_subscription.php
        <a href="index.php?page=grade">
            <img src="assets/images/fleche_retour.png" alt="Flèche de retour">
=======
        <a href="/grade.php">
            <img src="assets/fleche_retour.png" alt="Flèche de retour">
>>>>>>> Mouad:grade_subscription.php
            Retourner aux grades
        </a>
    </button>
</div>

<div>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Grade <?php echo htmlspecialchars($grade[0]['nom_grade']); ?></td>
                    <td>1</td>
                    <td><?= number_format(htmlspecialchars($grade[0]['prix_grade']), 2, ',', ' ') ?> €</td>
                    <td><?= number_format(htmlspecialchars($grade[0]['prix_grade']), 2, ',', ' ') ?> €</td>
                </tr>
            </tbody>
        </table>

        <h3>Total &nbsp : &nbsp<?= number_format(htmlspecialchars($grade[0]['prix_grade']), 2, ',', ' ') ?> €</h3>
    </div>

    <div>    
        <h3>Paiement</h3>

        <label for="mode_paiement">Mode de Paiement :</label>
        <select id="mode_paiement" name="mode_paiement" required>
            <option value="carte_credit">Carte de Crédit</option>
            <option value="paypal">PayPal</option>
        </select><br><br>
        <div id="carte_credit" class="mode_paiement_fields">
<<<<<<< HEAD:app/views/grade_subscription.php
            <form method="POST" action="index.php?page=grade_subscription.php&id=<?= $id_grade ?>">
=======
            <form method="POST" action="grade_subscription.php?id=<?= $id_grade ?>">
>>>>>>> Mouad:grade_subscription.php
                <input type="hidden" name="mode_paiement" value="carte_credit">

                <label for="numero_carte">Numéro de Carte :</label>
                <input type="text" id="numero_carte" name="numero_carte" placeholder="XXXX XXXX XXXX XXXX" required><br><br>

                <label for="expiration">Date d'Expiration :</label>
                <input type="text" id="expiration" name="expiration" placeholder="MM/AA" required><br><br>

                <label for="cvv">CVV :</label>
                <input type="text" id="cvv" name="cvv" placeholder="XXX" required><br><br>

                <button type="submit" id="finalise-order-button">Valider l'adhésion</button>
            </form>
        </div>
        <div id="paypal" class="mode_paiement_fields" style="display: none;">
<<<<<<< HEAD:app/views/grade_subscription.php
            <form method="POST" action="index.php?page=grade_subscription.php&id=<?= $id_grade ?>">
=======
            <form method="POST" action="grade_subscription.php?id=<?= $id_grade ?>">
>>>>>>> Mouad:grade_subscription.php
                <input type="hidden" name="mode_paiement" value="paypal">

                <button type="button" id="paypal-button">Se connecter à PayPal</button><br><br>
                    
                <button type="submit" id="finalise-order-button2">Valider l'adhésion</button>
            </form>
        </div>
    </div>
</div>



<script>
    document.getElementById('mode_paiement').addEventListener('change', function() {
        var modePaiement = this.value;
        if (modePaiement === 'carte_credit') {
            document.getElementById('carte_credit').style.display = 'block';
            document.getElementById('paypal').style.display = 'none';
        } else if (modePaiement === 'paypal') {
            document.getElementById('carte_credit').style.display = 'none';
            document.getElementById('paypal').style.display = 'block';
        }
    });
</script>

<?php require_once "app/views/footer.php" ?>

</body>
</html>