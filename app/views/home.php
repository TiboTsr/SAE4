<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Accueil</title>
    <link rel="stylesheet" href="assets/styles/index_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">
    <link rel="stylesheet" href="assets/styles/bubble.css">
</head>
<body id="index" class="body_margin">

    <?php require_once 'header.php'; ?>

    <div id="page-container">

        <section>
            <h2 class="titre_vertical">ADIIL</h2>
            <div id="index_carrousel">
                <img src="assets/images/photo_accueil_BDE.png" alt="Carrousel ADIIL">
            </div>
        </section>

        <section>
            <div class="paragraphes">
                <p>
                    <b class="underline">L'ADIIL</b>, ou l'<b>Association</b> du <b>Département</b>
                    <b>Informatique</b> de l'<b>IUT</b> de <b>Laval</b>,
                    est une organisation étudiante dédiée à créer un environnement propice à l'épanouissement dans le campus.
                    Participer à des événements, et plus globalement à la vie du département.
                </p>
                <p>
                    L'ADIIL, véritable moteur de la vie étudiante à l'IUT de Laval,
                    offre un cadre propice à l'épanouissement académique et social des étudiants en informatique.
                    En participant à ses événements variés, les étudiants enrichissent leur expérience universitaire,
                    tout en renforçant les liens au sein de la communauté.
                </p>
            </div>
            <h2 class="titre_vertical">L'ASSO</h2>
        </section>

        <section>
            <h2 class="titre_vertical">SCORES</h2>
            <div id="podium">
                <?php foreach ([2, 1, 3] as $memberNumber):
                    $pod = $podium[$memberNumber - 1]; ?>
                <div class="podium_unit">
                    <h3>#0<?php echo $memberNumber; ?></h3>
                    <h4><?php echo htmlspecialchars($pod['prenom_membre']); ?></h4>
                    <div>
                        <?php if ($pod['pp_membre'] == null): ?>
                            <img src="admin/ressources/default_images/user.jpg" alt="Profile Picture" class="profile_picture">
                        <?php else: ?>
                            <img src="api/files/<?php echo htmlspecialchars($pod['pp_membre']); ?>" alt="Profile Picture" class="profile_picture">
                        <?php endif; ?>
                        <?php echo $pod['xp_membre']; ?> xp
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <div class="events-display">
                <?php foreach ($eventsDisplay as $item):
                    $event = $item['data'];
                    $moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                               7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
                    $eventDate = substr($event['date_evenement'], 0, 10);
                    $eventDateInfo = getdate(strtotime($eventDate));
                    $dateFormatted = ucwords($eventDateInfo['mday'] . ' ' . $moisFr[$eventDateInfo['mon']] . ', ' . $event['lieu_evenement']);
                ?>
                <div class="event" event-id="<?php echo $event['id_evenement']; ?>">
                    <div>
                        <h2><?php echo htmlspecialchars($event['nom_evenement']); ?></h2>
                        <?php echo $dateFormatted; ?>
                    </div>
                    <h4 class="<?php echo $item['class']; ?>">
                        <?php echo $item['label']; ?>
                    </h4>
                </div>
                <?php endforeach; ?>
                <h3><a href="index.php?page=events">Voir tous les événements</a></h3>
            </div>
            <h2 class="titre_vertical">EVENT</h2>
        </section>

    </div>

    <?php require_once 'footer.php'; ?>
    <script src="assets/scripts/event_details_redirect.js"></script>
    <script src="assets/scripts/bubble.js"></script>
</body>
</html>