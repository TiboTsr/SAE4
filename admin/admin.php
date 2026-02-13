<?php

session_start();
include_once '../api/tools.php';


if(!isset($_SESSION['userid'])){
    header('Location: ../login.php');
    exit();
}
if (!(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'])){
    header('Location: /admin/panels/unauthorized.html');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BDE - Administration</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="ressources/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/admin.css">

</head>
<body id="main">

    <!-- Navigation -->
    <nav>

        <h1 onclick="window.location.href='/'" style="cursor: pointer;">ADIIL - Admin</h1>

        <ul>

            <li perm="chat">
                    <img src="ressources/panels_icons/chat.svg" alt="Icône chat, bulle de discussion pour accès au chat">
                <p>Chat</p>
            </li>
            
            <?php
                if (tools::hasPermission('p_boutique')){
                    echo '
                        <li perm="boutique">
                                <img src="ressources/panels_icons/boutique.svg" alt="Icône boutique, panier ou sac pour accès à la boutique">
                            <p>Boutique</p>
                        </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_utilisateur')){
                    echo '
            <li perm="utilisateurs">
                    <img src="ressources/panels_icons/users.svg" alt="Icône utilisateurs, silhouette ou groupe pour gestion des utilisateurs">
                <p>Utilisateurs</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_grade')){
                    echo '
            <li perm="grades">
                    <img src="ressources/panels_icons/grades.svg" alt="Icône grades, médaille ou étoile pour gestion des grades">
                <p>Grades</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_evenement')){
                    echo '
            <li perm="evenements">
                    <img src="ressources/panels_icons/events.svg" alt="Icône événements, calendrier ou horloge pour gestion des événements">
                <p>Evenements</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_comptabilite')){
                    echo '
            <li perm="comptabilite">
                    <img src="ressources/panels_icons/comptabilite.svg" alt="Icône comptabilité, calculatrice ou graphique pour gestion comptable">
                <p>Comptabilite</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_reunion')){
                    echo '
            <li perm="reunions">
                    <img src="ressources/panels_icons/reunions.svg" alt="Icône réunions, table ou personnes pour gestion des réunions">
                <p>Réunions</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_role')){
                    echo '
            <li perm="roles">
                    <img src="ressources/panels_icons/roles.svg" alt="Icône rôles, clé ou badge pour gestion des rôles">
                <p>Rôles</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_actualite')){
                    echo '
            <li perm="actualites">
                    <img src="ressources/panels_icons/actualite.svg" alt="Icône actualités, journal ou flux pour gestion des actualités">
                <p>Actualités</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_boutique')){
                    echo '
            <li perm="history">
                    <img src="ressources/panels_icons/history.svg" alt="Icône historique d\'achat, horloge ou liste pour suivi des achats">
                <p>Historique d\'achats</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_log')){
                    echo '
            <li perm="logs">
                    <img src="ressources/panels_icons/logs.svg" alt="Icône logs du serveur, document ou liste pour suivi des logs">
                <p>Logs du serveur</p>
            </li>
                        ';
                }
            ?>

        </ul>
    </nav>

    <!-- Permissions -->
    <main>
        <iframe frameborder="0" id="content" src="./panels/chat.html"></iframe>
    </main>

    <!-- SCRIPT -->
    <script type="module" src="scripts/admin.js"></script>

</body>
</html>