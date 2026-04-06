<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Evenements</title>

    <link rel="stylesheet" href="styles/events_style.css">
    <link rel="stylesheet" href="styles/general_style.css">
    <link rel="stylesheet" href="styles/header_style.css">
    <link rel="stylesheet" href="styles/footer_style.css">
>>>>>>> Mouad:events.php
</head>
<body class="body_margin">

    <?php require_once 'app/views/header.php'; ?>

    <h1>LES EVENEMENTS</h1>
    <section>
        <a class="show-more" href="index.php?page=events&show=<?php echo $show + 10; ?>">Voir plus loin dans le passé</a>
        <div class="events-display">
            <?php foreach ($eventsDisplay as $item):
                $event = $item['data'];
                $eventDateInfo = $item['dateInfo'];
            ?>
            <div class="event-box <?php echo $item['otherClasses']; ?>" id="<?php echo $item['closestId']; ?>">
                <div class="timeline-event">
                    <h4><?php echo ucwords($joursFr[$eventDateInfo['wday']] . ' ' . $eventDateInfo['mday'] . ' ' . $moisFr[$eventDateInfo['mon']]); ?></h4>
                    <div class="vertical-line"></div>
                    <p><?php echo $item['datePinLabel']; ?></p>
                    <div class="timeline-marker <?php echo $item['datePinClass']; ?>">
                        <div class="time-line"></div>
                    </div>
                </div>
                <div class="event" event-id="<?php echo $event['id_evenement']; ?>">
                    <div>
                        <h2><?php echo htmlspecialchars($event['nom_evenement']); ?></h2>
                        <?php echo ucwords(htmlspecialchars($event['lieu_evenement'])); ?>
                    </div>
                    <h4 class="<?php echo $item['eventClass']; ?>">
                        <?php echo $item['eventLabel']; ?>
                    </h4>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php require_once 'app/views/footer.php'; ?>
    <script src="scripts/event_details_redirect.js"></script>
    <script src="scripts/scroll_to_closest_event.js"></script>
</body>
</html>