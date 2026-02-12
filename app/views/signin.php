<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="assets/styles/login_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">

</head>
    <body>
        <?php 
            require_once 'app/views/header.php';
        ?>

        <form method="POST" action="index.php?page=signin" class="login-form">
            <h1>S'inscrire</h1>

            <label for="mail">Prénom :</label>
            <input type="text" name="fname">

            <label for="mail">Nom :</label>
            <input type="text" name="lname">
        
            <label for="mail">Adresse Mail :*</label>
            <input type="email" name="mail" required>

            <label for="password">Mot de passe :*</label>
            <input type="password" name="password" required>

            <label for="password">Confirmez le Mot de passe :*</label>
            <input type="password" name="password_verif" required>

            <button type="submit">Confirmer</button>
        </form>

        <!-- Gestion de l'inscription -->
        <?php
        if(!empty($selection_db)){
            echo 'Utilisateur déjà présent';
        }
        ?>
    </body>
</html>