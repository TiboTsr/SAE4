<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ ADIIL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="assets/styles/faq_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">
</head>
<body class="body_margin">

<?php require_once "app/views/header.php"; ?>

<h1>AIDE ET FAQ</h1>
<p class="help-intro">Retrouvez ici les acces utiles de l'ADIIL: Instagram, Discord, contact administration et questions frequentes.</p>

<section class="help-grid">
    <article class="help-card" id="instagram-posts">
        <h2>Instagram ADIIL</h2>
        <p>Accedez au compte Instagram de l'ADIIL pour suivre les dernieres publications et annonces du BDE.</p>
        <a class="help-link" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Voir les derniers posts</a>
    </article>

    <article class="help-card" id="discord-link">
        <h2>Compte Discord</h2>
        <p>Associez votre compte Discord pour recevoir les informations de l'ADIIL sur le serveur et centraliser vos notifications.</p>
        <button type="button" class="discord-button" id="discord-connect">Relier mon compte Discord</button>
    </article>

    <article class="help-card" id="contact-admin">
        <h2>Contacter un administrateur</h2>
        <p>Pour toute demande, ecrivez a un administrateur ADIIL via email.</p>
        <a class="help-link" href="mailto:aassociation.adiil@gmail.com">Contacter un administrateur</a>
    </article>

    <article class="help-card" id="faq-section">
        <h2>FAQ</h2>
        <div class="faq-list">
            <div class="faq-item">
                <h3>Comment devenir membre de l'ADIIL ?</h3>
                <p>Consultez la page A propos pour les conditions d'adhesion et le fonctionnement du bureau.</p>
            </div>
            <div class="faq-item">
                <h3>Ou voir les actualites ?</h3>
                <p>La page actualités centralise les dernieres publications et evenements.</p>
            </div>
            <div class="faq-item">
                <h3>Comment associer mon compte Discord ?</h3>
                <p>Cliquez sur "Relier mon compte Discord", et suivez les instructions.</p>
            </div>
        </div>
    </article>
</section>

<?php require_once "app/views/footer.php"; ?>

</body>
</html>
