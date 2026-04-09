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

    <?php require_once 'app/views/header.php'; ?>

    <div class="page-back-wrap">
        <a class="page-back" href="index.php?page=home">
            <img src="assets/images/fleche_retour.png" alt="Retour a l'accueil">
            Retour à l'accueil
        </a>
    </div>

    <h1>Les grades</h1>

    <?php if (isset($_SESSION['message'])) : ?>
        <?php $messageStyle = (isset($_SESSION['message_type']) && $_SESSION['message_type'] === 'error') ? 'error-message' : 'success-message'; ?>
        <div id="<?php echo $messageStyle; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <?php if (!empty($gradesDisplay)) : ?>
        <div id="product-list">
            <?php foreach ($gradesDisplay as $item) :
                $product = $item['data']; ?>
            <div id="one-product">
                <div>
                    <?php if (empty($product['image_grade'])) : ?>
                        <img src="admin/ressources/default_images/grade.webp" alt="Image du grade">
                    <?php else : ?>
                        <img src="<?php echo htmlspecialchars(resolveStoredImageSrc($product['image_grade'], 'admin/ressources/default_images/grade.webp')); ?>" alt="Image du grade">
                    <?php endif; ?>

                    <h3 title="<?php echo htmlspecialchars($product['nom_grade']); ?>">
                        <?php echo htmlspecialchars($product['nom_grade']); ?>
                    </h3>

                    <?php if (!empty($product['description_grade'])) : ?>
                        <p><?php echo htmlspecialchars($product['description_grade']); ?></p>
                    <?php endif; ?>

                    <p>-- Prix : <?php echo number_format($product['prix_grade'], 2, ',', ' '); ?> € --</p>
                </div>

                <div>
                    <p id="adhesion-status">
                        <?php if ($item['ownsGrade']) : ?>
                            <button id="detention">Vous détenez ce grade</button>
                        <?php else : ?>
                            <a id="buy-button" href="index.php?page=grade_subscription&id=<?php echo htmlspecialchars($product['id_grade']); ?>">
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

    <?php require_once 'app/views/footer.php'; ?>
</body>
</html>
