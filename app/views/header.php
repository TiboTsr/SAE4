<link rel="shortcut icon" href="admin/ressources/favicon.png" type="image/x-icon">

<?php
    @session_start();
    $isUserLoggedIn = isset($_SESSION['userid']);
    $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ;
?>


<!-- HEADER -->
<header>
    <a id="accueil" href="index.php">
<<<<<<< HEAD:app/views/header.php
        <img src="assets/images/logo.png" alt="Logo de l'ADIIL">
=======
        <img src="assets/logo.png" alt="Logo de l'ADIIL">
>>>>>>> Mouad:header.php
    </a>
    <nav>
        <ul>
            <li>
<<<<<<< HEAD:app/views/header.php
                <a href="index.php?page=events">Événements</a>
            </li>
            <li>
                <a href="index.php?page=news">Actualités</a>
            </li>
            <li>
                <a href="index.php?page=shop">Boutique</a>
            </li>
            <li>
                <a href="index.php?page=grade">Grades</a>
=======
                <a href="events.php">Événements</a>
            </li>
            <li>
                <a href="news.php">Actualités</a>
            </li>
            <li>
                <a href="shop.php">Boutique</a>
            </li>
            <li>
                <a href="grade.php">Grades</a>
>>>>>>> Mouad:header.php
            </li>
            
            <?php if ($isUserLoggedIn): ?>
                <li>
<<<<<<< HEAD:app/views/header.php
                    <a href="index.php?page=agenda">Agenda</a>
=======
                    <a href="agenda.php">Agenda</a>
>>>>>>> Mouad:header.php
                </li>
            <?php endif; ?>

            <li>
<<<<<<< HEAD:app/views/header.php
                <a href="index.php?page=about">À propos</a>
=======
                <a href="about.php">À propos</a>
>>>>>>> Mouad:header.php
            </li>

            <?php if ($isUserLoggedIn): ?>
                <li>
<<<<<<< HEAD:app/views/header.php
                    <a href="index.php?page=account">Mon compte</a>
=======
                    <a href="account.php">Mon compte</a>
>>>>>>> Mouad:header.php
                </li>

                <?php if ($isAdmin): ?>
                  <li>
                      <a id="header_admin" href="admin/admin.php">Panel Admin</a>
                  </li>
                <?php endif; ?>

            <?php else: ?>
                <li>
<<<<<<< HEAD:app/views/header.php
                    <a href="index.php?page=login">Se connecter</a>
=======
                    <a href="login.php">Se connecter</a>
>>>>>>> Mouad:header.php
                </li>
            <?php endif; ?>

      
        </ul>
    </nav>
</header>
