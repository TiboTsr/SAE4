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
            require_once 'header.php';
        ?>


        <!-- Formulaire de connexion -->
        <form method="POST" action="index.php?page=login" class="login-form">
            <h1>Connexion</h1>
            <label for="mail">Adresse Mail :</label>
            <input type="email" name="mail" required>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password">

            <button type="submit">Se connecter</button>
        </form>

        <form method="GET" action="index.php?page=signin" id="create-account">
            <h2>Pas encore de compte ?</h2>
            <button type="submit">Créez en un</button>
        </form>

    </body>
</html>