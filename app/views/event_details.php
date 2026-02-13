<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <title><?php echo $event['nom_evenement']?></title>

    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/event_details_style.css">
</head>

<body>
    <?php
    require_once 'app/views/header.php';
?>
    <section class="event-details">
        <?php if($event['image_evenement'] == null):?>
            <img src="admin/ressources/default_images/event.jpg" alt="Image de l'événement">
        <?php else:?>
            <img src="api/files/<?php echo $event['image_evenement']; ?>" alt="Image de l'événement">
        <?php endif?>

        <h1><?php echo strtoupper($event['nom_evenement']); ?></h1>

        <div>
            <h2>
                <?php echo date('d/m/Y', strtotime($event['date_evenement']));?>
            </h2>
            <?php if($event_date < $current_date):?>
                <button class="subscription" id="passed_subscription">Passé</button>
            <?php else:
                if($isSubscribed):
                    echo '<button class="subscription" id="passed_subscription">Inscrit</button>';
                else:?>
                    <form class="subscription" action="index.php?page=event_subscription" method="post">
                        <input type="text" name="eventid" value="<?php echo $eventid?>" hidden>
                        <button type="submit">Inscription</a></button>
                    </form>
                <?php endif;?>
            <?php endif;?>
        </div>

        <ul>
            <li>
                <div>📍<h3><?php echo $event['lieu_evenement']; ?></h3>
                </div>
            </li>
            <li>
                <div>💸<h3><?php echo $event['prix_evenement']; ?>€ par personne</h3>
                </div>
            </li>
            <?php if(boolval($event['reductions_evenement'])){echo "<li><div>💎<h3>-10% pour les membres Diamants</h3></div></li>";} ?>
        </ul>

        <p>
            <?php echo nl2br(htmlspecialchars($event['description_evenement'])); ?>
        </p>

    </section>


    <section class="gallery">
        <h2>GALLERIE</h2>
        <?php if($isLoggedIn):?>

            <h3>Mes photos</h3>
            <div class="my-medias">
                <?php
                
                foreach($mediasLogin as $media => $img):?>
                    <img src="api/files/<?php echo trim($img['url_media']);?>" alt="Image Personelle de l'événement">
                <?php endforeach;?>

                <form id="add-media" action="index.php?page=add_media" method="post" enctype="multipart/form-data">
                    <label for="file-picker">
                        <img src="assets/images/add_media.png" alt="Ajouter un média">
                    </label>
                    <input type="hidden" name="eventid" value="<?php echo $eventid?>">
                    <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']?>">

                    <input type="file" id="file-picker" name="file" accept="image/jpeg, image/png, image/webp" hidden>
                    <button type="submit" style="display:none;">Envoyer</button>
                </form>

                <form id="open-gallery" action="index.php?page=my_gallery" method="get">
                    <label for="open-gallery-button">
                        <img src="assets/images/explore_gallery.png" alt="Voir ma galerie entière">
                    </label>
                    <input type="hidden" name="eventid" value="<?php echo $eventid ?>">
                    <button id="open-gallery-button" type="submit" style="display:none;">Envoyer</button>
                </form>
            </div>
        <?php endif;?>
        <h3>Collection Generale</h3>

        <div class="general-medias">

            <?php foreach($medias as $media => $img):?>
                <img src="api/files/<?php echo trim($img['url_media']);?>" alt="Image de l'événement">
            <?php endforeach;?>


        </div>
        <div class="show-more">
            <form action="index.php?page=event_details" method="GET" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo $eventid?>">
                <input type="hidden" name="show" value="<?php echo $show + 8?>">

                <button type="submit">Voir plus</button>
            </form>

            <form action="index.php?page=event_details" method="GET" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo $eventid?>">
                <?php if($show >= 20): ?>
                <input type="hidden" name="show" value="<?php echo $show - 10?>">
                <?php endif;?>
                <button type="submit">Voir Moins</button>
            </form>
        </div>


    </section>


    <?php require_once 'app/views/footer.php';?>
    <script src="assets/scripts/open_media.js"></script>
    <script src="assets/scripts/add_media.js"></script>
    <script src="assets/scripts/open_gallery.js"></script>

</body>

</html>