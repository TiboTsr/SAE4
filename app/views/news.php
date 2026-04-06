<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <title>Actualités</title>
    <link rel="stylesheet" href="styles/news_style.css">

    <link rel="stylesheet" href="styles/general_style.css">
    <link rel="stylesheet" href="styles/header_style.css">
    <link rel="stylesheet" href="styles/footer_style.css">
</head>
<body class="body_margin">

<?php require_once 'app/views/header.php'; ?>

<h1>ACTUALITES</h1>
<section>
    <a class="show-more" href="index.php?page=news&show=<?= (int) $showMore ?>">Voir plus loin dans le passe</a>
    <div class="events-display">
        <?php foreach ($newsItems as $item): ?>
            <div class="event-box" id="<?= $item['is_closest'] ? 'closest-event' : '' ?>">
                <div class="timeline-event">
                    <h4><?= htmlspecialchars($item['date_label']) ?></h4>
                    <div class="vertical-line"></div>
                </div>
                <div class="event" event-id="<?= (int) $item['id_actualite'] ?>">
                    <div>
                        <h2 style="margin-bottom: 0px;"><?= htmlspecialchars($item['titre_actualite']) ?></h2>
                    </div>
                    <h4 class="event-not-subscribed">Consulter</h4>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once "app/views/footer.php" ?>

<script src="scripts/news_details_redirect.js"></script>
<script src="scripts/scroll_to_closest_event.js"></script>

</body>
</html>
