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
    <section class="event-details">
        <?php if($event['image_actualite'] == null):?>
            <img src="admin/ressources/default_images/event.jpg" alt="Image de l'actualite">
        <?php else:?>
            <img src="api/files/<?php echo $event['image_actualite']; ?>" alt="Image de l'actualite">
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

    </section>


    <?php require_once 'app/views/footer.php';?>
</body>

</html>