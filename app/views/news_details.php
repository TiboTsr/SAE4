<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <title><?php echo $event['titre_actualite']?></title>

    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

    <link rel="stylesheet" href="assets/styles/event_details_style.css">
</head>

<body>
    <?php
        require_once 'app/views/header.php';
    ?>
    <div class="page-back-wrap">
        <a class="page-back" href="index.php?page=news">
            <img src="assets/images/fleche_retour.png" alt="Retour aux actualites">
            Retour aux actualites
        </a>
    </div>
    <section class="event-details">
        <?php if ($event['image_actualite'] == null) :?>
            <img src="admin/ressources/default_images/event.jpg" alt="Image de l'actualite">
        <?php else :?>
            <img src="<?php echo htmlspecialchars(resolveStoredImageSrc($event['image_actualite'], 'admin/ressources/default_images/event.jpg')); ?>" alt="Image de l'actualite">
        <?php endif?>
        <h1><?php echo strtoupper($event['titre_actualite']); ?></h1>

        <div>
            <h2>
                <?php
                    //$current_date = new DateTime(date("Y-m-d"));
                    //$event_date = new DateTime(substr($event['date_actualite'], 0, 10));
                    echo date('d/m/Y', strtotime($event['date_actualite']));
                ?>
            </h2>
        </div>
        <ul></ul>
        <p>
            <?php echo nl2br(htmlspecialchars($event['contenu_actualite'])); ?>
        </p>

        <div class="news-nav-buttons">
            <?php if (!is_null($newerNewsId)) : ?>
                <a class="news-nav-button news-nav-button-left" href="index.php?page=news_details&id=<?= (int) $newerNewsId ?>">
                    <span class="news-nav-icon" aria-hidden="true">&#8592;</span>
                    <span>Actualite plus recente</span>
                </a>
            <?php endif; ?>

            <?php if (!is_null($olderNewsId)) : ?>
                <a class="news-nav-button news-nav-button-right" href="index.php?page=news_details&id=<?= (int) $olderNewsId ?>">
                    <span>Actualite plus ancienne</span>
                    <span class="news-nav-icon" aria-hidden="true">&#8594;</span>
                </a>
            <?php endif; ?>
        </div>

    </section>


    <?php require_once 'app/views/footer.php';?>
</body>

</html>
