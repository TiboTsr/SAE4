<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Accueil</title>
    <link rel="stylesheet" href="assets/styles/home_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">
    <link rel="stylesheet" href="assets/styles/bubble.css">
</head>
<body id="index" class="body_margin">

    <?php
     require_once 'app/views/header.php';
    ?>
    <div id="bubble-layer" aria-hidden="true"></div>
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
                <?php foreach ([2, 1, 3] as $memberNumber) :
                    $pod = $podium[$memberNumber - 1]; ?>
                <div class="podium_unit">
                    <h3>#0<?php echo $memberNumber; ?></h3>
                    <h4><?php echo htmlspecialchars($pod['prenom_membre']); ?></h4>
                    <div>
                        <?php if ($pod['pp_membre'] == null) : ?>
                            <img src="admin/ressources/default_images/user.jpg" alt="Profile Picture" class="profile_picture">
                        <?php else : ?>
                            <img src="<?php echo htmlspecialchars(resolveStoredImageSrc($pod['pp_membre'], 'admin/ressources/default_images/user.jpg')); ?>" alt="Profile Picture" class="profile_picture">
                        <?php endif; ?>
                        <?php echo $pod['xp_membre']; ?> xp
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h2 class="titre_vertical">EVENT</h2>
                <div class="events-display">
                    <?php foreach ($eventsDisplay as $event) :
                        if (!isset($event['data']) || $event['data'] === null) {
                            continue;
                        }

                        $data = $event['data'];

                        if (!isset($data["id_evenement"], $data["nom_evenement"], $data["lieu_evenement"], $data["date_evenement"])) {
                            continue;
                        }

                        $eventid = $data["id_evenement"];
                        $event_date_info = getdate(strtotime($data["date_evenement"]));
                        ?>
    
                    <div class="event" event-id="<?php echo $eventid;?>">
                        <div>
                            <h2><?php echo htmlspecialchars($data['nom_evenement']); ?></h2>
                            <p>
                                <?php
                                echo ucwords(
                                    $event_date_info["mday"] . " " .
                                    $moisFr[$event_date_info['mon']] . ", " .
                                    htmlspecialchars($data["lieu_evenement"])
                                );
                                ?>
                            </p>
                        </div>
                            
                        <h4 class="<?php echo $event['class'] ?? ''; ?>">
                            <?php echo $event['label'] ?? ''; ?>
                        </h4>
                    </div>
                            
                    <?php endforeach; ?>
                        
                <h3><a href="index.php?page=events">Voir tous les événements</a></h3>
            </div>
        </section>

    </div>
    <?php require_once 'app/views/footer.php';?>
    <script src="assets/scripts/event_details_redirect.js"></script>
    <script src="assets/scripts/bubble.js"></script>
</body>
</html>
