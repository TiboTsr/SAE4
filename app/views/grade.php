<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="assets/styles/grade_style.css">

    <link rel="stylesheet" href="assets/styles/general_style.css">

    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

</head>
<body class="body_margin">

<!-- Importer les fichiers -->
<?php 
require_once "app/views/header.php" ;

?>

<H1>Les grades</H1>

<!-- Affichage du message de succès ou d'erreur -->
<div>
    <?php
        if (isset($_SESSION['message'])) {
            $messageStyle = isset($_SESSION['message_type']) && $_SESSION['message_type'] === "error" ? "error-message" : "success-message";
            echo '<div id="' . $messageStyle . '">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']); // Supprimer le message après affichage
            unset($_SESSION['message_type']); // Supprimer le type après affichage
        }
    ?>
</div>

<?php if (!empty($products)) : ?>
    <div id="product-list">
        <?php foreach ($products as $product) : ?>
                <div id="one-product">
                    <div>
                        <?php if($product['image_grade'] == null):?>
                            <img src="admin/ressources/default_images/grade.webp" alt="Présentation du grade" />
                        <?php else:?>
                            <img src="api/files/<?php echo $product['image_grade']; ?>" alt="Présentation du grade" />
                        <?php endif?>

                        <h3 title="<?= htmlspecialchars($product['nom_grade']) ?>">
                            <?= htmlspecialchars($product['nom_grade']) ?>
                        </h3>
                        <?php if (!empty($product['description_grade'])) { ?>
                            <p><?= htmlspecialchars($product['description_grade'])?></p>
                        <?php } ?>
                        <p>-- Prix : <?= number_format(htmlspecialchars($product['prix_grade']), 2, ',', ' ') ?> € --</p>
                    </div>
                    <div>
                        <p id="adhesion-status">

                            <?php
                            if (!empty($_SESSION['userid'])) {
                                $unAdherant = $db->select("SELECT * FROM GRADE INNER JOIN ADHESION ON GRADE.id_grade = ADHESION.id_grade INNER JOIN MEMBRE ON ADHESION.id_membre = MEMBRE.id_membre WHERE GRADE.id_grade = ? AND MEMBRE.id_membre = ?;",
                                "ii",
                                [$product['id_grade'], $_SESSION['userid']]
                                );
                                ?>
                            <?php } ?>
                            <?php if (!empty($_SESSION) && !empty($unAdherant)): ?>
                                <button id="detention">Vous détenez ce grade</button>
                            <?php else: ?>
                                <a id="buy-button" href="index.php?page=grade_subscription&id=<?= htmlspecialchars($product['id_grade']) ?>">
                                    Acheter
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p>Aucun grade trouvé.</p>
<?php endif; ?>




<?php require_once "app/views/footer.php" ?>


</body>
</html>