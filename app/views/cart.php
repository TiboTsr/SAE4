<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="assets/styles/cart_style.css">

    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

    <script>
        // Fonction pour valider la soumission du formulaire par la touche "Entree"
        function pressEnter(event) {
            var code = event.which || event.keyCode;
            if (code == 13) {
                document.getElementById("form-quantity").submit();
            }
        }
    </script>
</head>

<body class="body_margin">

<?php require_once "app/views/header.php"; ?>

<div>
    <h1>MON PANIER</h1>

    <div>
        <?php if (!empty($message)): ?>
            <?php $messageStyle = $messageType === "error" ? "error-message" : "success-message"; ?>
            <div id="<?= $messageStyle ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>

    <div>
        <button id="shop-button">
            <a href="index.php?page=shop">
                <img src="assets/images/fleche_retour.png" alt="Fleche de retour">
                Retourner à la boutique
            </a>
        </button>
    </div>
</div>

<?php if (!empty($_SESSION['cart'])) : ?>
<div id="cart-container">
    <form method="POST" action="index.php?page=cart" id="form-quantity">
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix unitaire</th>
                    <th>Quantite</th>
                    <th>Sous-total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                <tr>
                    <td id="article-cell">
                        <img src="api/files/<?php echo htmlspecialchars($product['image_article']); ?>" alt="Image de l'article" />
                        <p><?= htmlspecialchars($product['nom_article']) ?></p>
                    </td>
                    <td><?= number_format((float) $product['prix_article'], 2, ',', ' ') ?> &euro;</td>
                    <td><input type="text" name="cart[quantity][<?= (int) $product['id_article'] ?>]" value="<?= (int) $_SESSION['cart'][$product['id_article']] ?>" onkeydown="pressEnter(event)"></td>
                    <td><?= number_format((float) $product['prix_article'] * (int) $_SESSION['cart'][$product['id_article']], 2, ',', ' ') ?> &euro;</td>
                    <td>
                        <a href="index.php?page=cart&del=<?= (int) $product['id_article'] ?>">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Nombre d'articles :</th>
                    <td><?= $cart->count() ?></td>
                </tr>
                <tr>
                    <th>Total :</th>
                    <td><?= number_format($cart->total(), 2, ',', ' ') ?> &euro;</td>
                </tr>

                <?php if ($totalWithReduc !== null): ?>
                    <tr>
                        <th style="min-width: 400px">Total apres reductions :</th>
                        <td style="min-width: 50px"><?= number_format($totalWithReduc, 2, ',', ' ') ?> &euro;</td>
                    </tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </form>
</div>
<div>
    <form class="subscription" action="index.php?page=order" method="post">
        <?php if (!empty($_SESSION['cart'])): ?>
            <input type="hidden" name="cart" value='<?= htmlspecialchars(json_encode($_SESSION['cart'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'>
        <?php endif; ?>
        <button type="submit" id="order-button">
            Commander
        </button>
    </form>
</div>

<?php else : ?>
    <p id="empty-cart">Votre panier est vide</p>
<?php endif; ?>

<?php require_once "app/views/footer.php"; ?>

</body>
</html>
