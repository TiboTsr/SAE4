
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="assets/styles/event_subscription_style.css">

    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">
</head>
<body class="body_margin">

    <?php require_once "app/views/header.php"; ?>

    <h1>INSCRIPTION</h1>

    <div>
        <button id="cart-button">
            <a href="/event_details.php?id=<?php echo $eventid?>">
                <img src="/assets/fleche_retour.png" alt="Flèche de retour, retourne à la page de l'évènement">
                Retourner à l'évènement
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
                        <td><?php echo strtoupper(htmlspecialchars($title)); ?></td>
                        <td>1</td>
                        <td><?= number_format($price, 2, ',', ' ') ?> €</td>
                        <td><?= number_format($price, 2, ',', ' ') ?> €</td>
                    </tr>
                </tbody>
            </table>

            <h3>Total &nbsp : &nbsp <?= number_format($price, 2, ',', ' ') ?> €</h3>
            <?php         
                var_dump($price);
                var_dump($user_reduction);
            ?>
            <h3>Total après réductions &nbsp : &nbsp <?= number_format($price*$user_reduction, 2, ',', ' ') ?> €</h3>
                   
        </div>

        <div>    
            <h3>Paiement</h3>

            <label for="mode_paiement">Mode de Paiement :</label>
            <select id="mode_paiement" name="mode_paiement" required>
                <option value="carte_credit">Carte de Crédit</option>
                <option value="paypal">PayPal</option>
            </select><br><br>
            <div id="carte_credit" class="mode_paiement_fields">
                <form method="POST" action="index.php?page=event_subscription">
                    <input type="hidden" name="eventid" value="<?php echo $eventid; ?>">
                    <input type="hidden" name="price" value="<?php echo $price*$user_reduction; ?>">
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
                <form method="POST" action="index.php?page=event_subscription">
                    <input type="hidden" name="eventid" value="<?php echo $eventid; ?>">
                    <input type="hidden" name="price" value="<?php echo $price*$user_reduction; ?>">
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
