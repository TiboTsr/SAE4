<link rel="shortcut icon" href="admin/ressources/favicon.png" type="image/x-icon">

<!-- HEADER -->
<header>
    <a id="accueil" href="index.php">
        <img src="assets/images/logo.png" alt="Logo de l'ADIIL">
    </a>
    <button
        class="nav-toggle"
        type="button"
        aria-expanded="false"
        aria-controls="site-navigation"
        aria-label="Ouvrir le menu de navigation"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>
    <nav id="site-navigation">
        <ul>
            <li>
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
            </li>
            
            <?php if ($isUserLoggedIn) : ?>
                <li>
                    <a href="index.php?page=agenda">Agenda</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="index.php?page=about">À propos</a>
            </li>

            <?php if ($isUserLoggedIn) : ?>
                <li>
                    <a href="index.php?page=account">Mon compte</a>
                </li>

                <?php if ($isAdmin) : ?>
                  <li>
                      <a id="header_admin" href="admin/admin.php">Panel Admin</a>
                  </li>
                <?php endif; ?>

            <?php else : ?>
                <li>
                    <a href="index.php?page=login">Se connecter</a>
                </li>
            <?php endif; ?>

      
        </ul>
    </nav>
</header>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const header = document.querySelector('header');
    const toggle = header ? header.querySelector('.nav-toggle') : null;
    const nav = document.getElementById('site-navigation');

    if (!header || !toggle || !nav) {
        return;
    }

    const closeMenu = function () {
        header.classList.remove('nav-open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', function () {
        const isOpen = header.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    nav.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 1100) {
            closeMenu();
        }
    });
});
</script>
