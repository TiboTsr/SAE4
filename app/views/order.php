<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commander</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="assets/styles/order_style.css">

    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

</head>

<body class="body_margin">


<?php
    require_once "app/views/header.php";
?>

<h1>MA COMMANDE</h1>

<div>
    <button id="cart-button" >
        <a href="index.php?page=cart">
            <img src="assets/images/fleche_retour.png" alt="Fleche de retour">
            Retourner au panier
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
                <?php foreach ($cart_items as $product_id => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nom_article']); ?></td>
                        <td><?php echo $item['quantite']; ?></td>
                        <td><?php echo number_format($item['prix_article'], 2, ',', ' ') . " €"; ?></td>
                        <td><?php echo number_format($item['prix_article'] * $item['quantite'], 2, ',', ' ') . " €"; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total &nbsp : &nbsp<?php echo number_format($total, 2, ',', ' '); ?> €</h3>
        <?php if (!empty($_SESSION['userid']) && !empty($adherant)) {?>
            <h3>Total après réductions &nbsp : &nbsp <?= number_format($totalWithReduc, 2, ',', ' ') ?> €</h3>  
        <?php }?>
    </div>

    <div>    
        <h3>Paiement</h3>

        <label for="mode_paiement">Mode de Paiement :</label>
        <select id="mode_paiement" name="mode_paiement" required>
            <option value="carte_credit">Carte de Crédit</option>
            <option value="paypal">PayPal</option>
        </select><br><br>
        <div id="carte_credit" class="mode_paiement_fields">
            <form method="POST" action="index.php?page=order">
                <input type="hidden" name="mode_paiement" value="carte_credit">

                <label for="numero_carte">Numéro de Carte :</label>
                <input type="text" id="numero_carte" name="numero_carte" placeholder="XXXX XXXX XXXX XXXX" required><br><br>

                <label for="expiration">Date d'Expiration :</label>
                <input type="text" id="expiration" name="expiration" placeholder="MM/AA" required><br><br>

                <label for="cvv">CVV :</label>
                <input type="text" id="cvv" name="cvv" placeholder="XXX" required><br><br>

                <button type="submit" id="finalise-order-button">Valider la commande</button>
            </form>
        </div>
        <div id="paypal" class="mode_paiement_fields" style="display: none;">
            <form method="POST" action="index.php?page=order">
                <input type="hidden" name="mode_paiement" value="paypal">

                <button type="button" id="paypal-button">Se connecter à PayPal</button><br><br>
                    
                <button type="submit" id="finalise-order-button">Valider la commande</button>
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
