<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adhérer</title>
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

<h1>MON ADHESION</h1>

<?php if (isset($_SESSION['message'])) : ?>
    <?php $messageStyle = (isset($_SESSION['message_type']) && $_SESSION['message_type'] === 'error') ? 'error-message' : 'success-message'; ?>
    <div id="<?php echo $messageStyle; ?>"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<div class="page-back-wrap">
    <a class="page-back" href="index.php?page=grade">
        <img src="assets/images/fleche_retour.png" alt="Flèche de retour">
        Retourner aux grades
    </a>
</div>

<div class="order-layout">
    <section class="order-summary">
        <h2>Récapitulatif</h2>
        <table>
            <thead>
                <tr>
                    <th>Grade</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($grade['nom_grade']); ?></td>
                    <td>1</td>
                    <td><?= number_format($prix, 2, ',', ' ') ?> €</td>
                    <td><?= number_format($prix, 2, ',', ' ') ?> €</td>
                </tr>
            </tbody>
        </table>

        <h3>Total : <?= number_format($prix, 2, ',', ' ') ?> €</h3>
    </section>

    <section class="order-payment">
        <h2>Validation</h2>
        <form method="POST" action="index.php?page=grade_subscription&id=<?php echo $id_grade; ?>" id="grade-order-form">
            <input type="hidden" name="mode_paiement" id="mode_paiement_hidden" value="carte_credit">

            <fieldset class="personal-info">
                <legend>Informations personnelles</legend>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="firstname">Prénom</label>
                        <input type="text" id="firstname" value="<?php echo htmlspecialchars($checkoutProfile['prenom']); ?>" readonly>
                    </div>
                    <div class="field-group">
                        <label for="lastname">Nom</label>
                        <input type="text" id="lastname" value="<?php echo htmlspecialchars($checkoutProfile['nom']); ?>" readonly>
                    </div>
                    <div class="field-group field-full">
                        <label for="email">Adresse mail</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($checkoutProfile['email']); ?>" readonly>
                    </div>
                    <div class="field-group">
                        <label for="tp">TP</label>
                        <input type="text" id="tp" value="<?php echo htmlspecialchars($checkoutProfile['tp']); ?>" readonly>
                    </div>
                </div>
            </fieldset>

            <div class="field-group">
                <label for="mode_paiement">Mode de Paiement</label>
                <select id="mode_paiement" required>
                    <option value="carte_credit">Carte de Crédit</option>
                    <option value="paypal">PayPal</option>
                    <option value="Especes">Espèces</option>
                </select>
            </div>

            <div id="carte_credit" class="mode_paiement_fields">
                <div class="form-grid">
                    <div class="field-group field-full">
                        <label for="numero_carte">Numéro de Carte</label>
                        <input type="text" id="numero_carte" name="numero_carte" placeholder="XXXX XXXX XXXX XXXX" required>
                    </div>
                    <div class="field-group">
                        <label for="expiration">Date d'Expiration</label>
                        <input type="text" id="expiration" name="expiration" placeholder="MM/AA" required>
                    </div>
                    <div class="field-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="XXX" required>
                    </div>
                </div>
            </div>

            <div id="paypal" class="mode_paiement_fields" style="display: none;">
                <button type="button" id="paypal-button">Se connecter à PayPal</button>
                <p class="payment-hint">Vous serez redirigé vers votre espace PayPal lors de la finalisation.</p>
            </div>

            <div id="especes" class="mode_paiement_fields" style="display: none;">
                <p>Paiement en espèces à la récupération de votre adhésion.</p>
            </div>

            <button type="submit" class="finalise-order-button">Valider l'adhésion</button>
        </form>
    </section>
</div>

<script>
    const modeSelect = document.getElementById('mode_paiement');
    const hiddenMode = document.getElementById('mode_paiement_hidden');
    const cardFields = ['numero_carte', 'expiration', 'cvv'].map(id => document.getElementById(id));

    function setCardRequired(isRequired) {
        cardFields.forEach(field => {
            if (!field) {
                return;
            }
            field.required = isRequired;
        });
    }

    modeSelect.addEventListener('change', function() {
        var modePaiement = this.value;
        hiddenMode.value = modePaiement;

        if (modePaiement === 'carte_credit') {
            document.getElementById('carte_credit').style.display = 'block';
            document.getElementById('paypal').style.display = 'none';
            document.getElementById('especes').style.display = 'none';
            setCardRequired(true);
        } else if (modePaiement === 'paypal') {
            document.getElementById('carte_credit').style.display = 'none';
            document.getElementById('paypal').style.display = 'block';
            document.getElementById('especes').style.display = 'none';
            setCardRequired(false);
        } else if (modePaiement === 'Especes') {
            document.getElementById('carte_credit').style.display = 'none';
            document.getElementById('paypal').style.display = 'none';
            document.getElementById('especes').style.display = 'block';
            setCardRequired(false);
        }
    });

    modeSelect.dispatchEvent(new Event('change'));
</script>

<?php require_once "app/views/footer.php" ?>

</body>
</html>
