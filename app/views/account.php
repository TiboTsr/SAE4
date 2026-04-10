<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="assets/styles/account_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">
</head>
<body class="body_margin">

    <?php require_once 'app/views/header.php'; ?>

    <div class="page-back-wrap">
        <a class="page-back" href="index.php?page=home">
            <img src="assets/images/fleche_retour.png" alt="Retour à l'accueil">
            Retour à l'accueil
        </a>
    </div>

    <h2>MON COMPTE</h2>

    <?php if (isset($_SESSION['message'])) : ?>
        <?php $toastType = (isset($_SESSION['message_type']) && $_SESSION['message_type'] === 'error') ? 'error' : ''; ?>
        <div id="account-toast" class="toast-container <?php echo $toastType; ?>" role="status" aria-live="polite" aria-atomic="true">
            <p class="toast"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <section>
        <div id="account-generalInfo">

            <!-- Photo de profil -->
            <div class="account-profile-block">
                <form method="POST" enctype="multipart/form-data" id="pp-form">
                    <label id="cadre-pp" for="profilePictureInput">
                        <?php if (empty($infoUser[0]['pp_membre'])) : ?>
                            <img src="admin/ressources/default_images/user.jpg" alt="Photo de profil">
                        <?php else : ?>
                            <img src="<?php echo htmlspecialchars(resolveStoredImageSrc($infoUser[0]['pp_membre'], 'admin/ressources/default_images/user.jpg')); ?>" alt="Photo de profil">
                        <?php endif; ?>
                    </label>
                    <input type="file" id="profilePictureInput" name="file" accept="image/jpeg, image/png, image/webp" style="display:none;" onchange="this.form.submit()">
                    <button type="button" id="edit-icon" onclick="document.getElementById('profilePictureInput').click()">
                        <img src="assets/images/edit_logo.png" alt="Ã‰diter la photo de profil">
                    </button>
                </form>
            </div>

            <!-- XP -->
            <div class="account-xp-block">
                <p><?php echo htmlspecialchars($infoUser[0]['xp_membre']); ?></p>
                <p>XP</p>
            </div>

            <!-- Grade -->
            <div id="cadre-grade">
                <?php if (empty($infoUser[0]['nom_grade'])) : ?>
                    <p>Vous n'avez pas de grade</p>
                <?php else : ?>
                    <p><?php echo htmlspecialchars($infoUser[0]['nom_grade']); ?></p>
                    <?php if (empty($infoUser[0]['image_grade'])) : ?>
                        <img src="admin/ressources/default_images/grade.webp" alt="Image du grade">
                    <?php else : ?>
                        <img src="<?php echo htmlspecialchars(resolveStoredImageSrc($infoUser[0]['image_grade'], 'admin/ressources/default_images/grade.webp')); ?>" alt="Image du grade">
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informations personnelles -->
        <form method="POST" action="index.php?page=account" id="account-personalInfo-form">
            <div>
                <div>
                    <label for="name">Prenom</label>
                    <input type="text" id="name" name="name" placeholder="PrÃ©nom"
                        value="<?php echo htmlspecialchars($infoUser[0]['prenom_membre']); ?>" required>
                    <label for="lastName">Nom</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Nom de famille"
                        value="<?php echo htmlspecialchars($infoUser[0]['nom_membre']); ?>" required>
                </div>
                <div>
                    <label for="mail">Adresse mail</label>
                    <input type="email" id="mail" name="mail" placeholder="Adresse mail"
                        value="<?php echo htmlspecialchars($infoUser[0]['email_membre']); ?>" required>
                    
                    <label for="tp">Groupe TP</label>
                    <select id="tp" name="tp">
                        <?php
                        $tpOptions = ['11A','11B','12C','12D','21A','21B','22C','22D','31A','31B','32C','32D'];
                        foreach ($tpOptions as $tp) :
                            $selected = $infoUser[0]['tp_membre'] === $tp ? 'selected' : '';
                            ?>
                            <option value="<?php echo $tp; ?>" <?php echo $selected; ?>>
                                TP <?php echo substr($tp, 0, 2) . ' ' . substr($tp, 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit">
                <img src="assets/images/save_logo.png" alt="Enregistrer">
            </button>
        </form>

        <!-- Modification du mot de passe -->
        <form method="POST" action="index.php?page=account" id="account-editPass-form">
            <div>
                <div>
                    <p>Modifier mon mot de passe :</p>
                    <input type="password" id="mdp" name="mdp" placeholder="Mot de passe actuel">
                    <input type="password" id="newMdp" name="newMdp" placeholder="Nouveau mot de passe" required>
                    <input type="password" id="newMdpVerif" name="newMdpVerif" placeholder="Confirmation du nouveau mot de passe" required>
                </div>
            </div>
            <button type="submit">
                <img src="assets/images/save_logo.png" alt="Enregistrer">
            </button>
        </form>
    </section>

    <section>
        <div id="buttons-section">
            <button type="button">
                <a href="https://discord.com/login" target="_blank">
                    <img src="assets/images/logo_discord.png" alt="Logo Discord">
                    Associer mon compte Discord
                </a>
            </button>

            <form action="index.php?page=account" method="post">
                <input type="hidden" name="deconnexion" value="true">
                <button type="submit">
                    <img src="assets/images/logOut_icon.png" alt="DÃ©connexion">
                    Déconnexion
                </button>
            </form>

            <form action="index.php?page=delete_account" method="post">
                <input type="hidden" name="delete_account" value="true">
                <button type="submit">
                    <img src="assets/images/delete_icon.png" alt="Suppression">
                    Supprimer mon compte
                </button>
            </form>
        </div>
    </section>

    <!-- Historique des achats -->
    <section id="section-mesAchats">
        <h2>MES ACHATS</h2>
        <div id="historique-achats">

            <?php if (!empty($historiqueAchats)) : ?>
                <table id="tab-historique-achats">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Mode de paiement</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historiqueAchats as $achat) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($achat['date_transaction']); ?></td>
                            <td><?php echo htmlspecialchars($achat['element']); ?></td>
                            <td><?php echo htmlspecialchars($achat['quantite']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($achat['montant'], 2, ',', ' ')); ?> â‚¬</td>
                            <td><?php echo htmlspecialchars($achat['mode_paiement']); ?></td>
                            <td><?php echo htmlspecialchars($achat['statut']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>Vous n'avez effectué aucun achat pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php require_once 'app/views/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('account-toast');

            if (!toast) {
                return;
            }

            toast.style.display = 'flex';

            requestAnimationFrame(function () {
                toast.classList.add('showed');
            });

            setTimeout(function () {
                toast.classList.remove('showed');
                setTimeout(function () {
                    toast.style.display = 'none';
                }, 200);
            }, 3000);
        });
    </script>
</body>
</html>


