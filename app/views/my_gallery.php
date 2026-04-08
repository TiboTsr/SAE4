<!DOCTYPE html>
<html lang="fr">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <title>Ma Gallerie</title>
    
    <link rel="stylesheet" href="assets/styles/my_gallery_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">

</head>
<body>
<?php
        require_once 'app/views/header.php';

        $isVideoMedia = static function (string $mediaPath): bool {
            $path = parse_url(trim($mediaPath), PHP_URL_PATH);
            $extension = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
            return in_array($extension, ['mp4', 'webm', 'ogg', 'mov'], true);
        };

?>


<section class="user-gallery">

    <a href="index.php?page=event_details&id=<?php echo "$eventid";?>" class="back-arrow">
        &#8592;<span>Retour</span> <!-- &#8592; est une flèche -->
    </a>
    <h1>MA GALLERIE</h1>
    <h2><?php echo $event['nom_evenement']?></h2>

    <div class="my-medias">

            <form id="add-media" action="index.php?page=add_media" method="post" enctype="multipart/form-data">
                <label for="file-picker">
                    <img src="assets/images/add_media.png" alt="Ajouter un média">
                </label>
                <input type="hidden" name="eventid" value="<?php echo $eventid?>">
                <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']?>">

                <input type="file" id="file-picker" name="file" accept="image/jpeg, image/png, image/webp, video/mp4, video/webm, video/ogg, video/quicktime" hidden>
                <button type="submit" style="display:none;">Envoyer</button>
            </form>

           <?php



            foreach ($medias as $media => $img) :?>
                <div class="media-container">
                    <?php $mediaUrl = 'api/files/' . trim($img['url_media']); ?>
                    <?php if ($isVideoMedia($img['url_media'])) : ?>
                        <video controls preload="metadata">
                            <source src="<?php echo htmlspecialchars($mediaUrl); ?>">
                        </video>
                    <?php else : ?>
                        <img src="<?php echo htmlspecialchars($mediaUrl); ?>" alt="Image Personnelle de l'événement">
                    <?php endif; ?>
                    <div class="delete-icon">

                        <form class="delete-media" action="index.php?page=delete_media" method="post">
                            <label for="del-media">
                                <img src="assets/images/delete_icon.png" alt="poubelle">
                            </label>
                            <input type="hidden" name="mediaid" value="<?php echo $img['id_media']?>">
                            <input type="hidden" name="eventid" value="<?php echo $eventid?>">

                            <button type="submit" style="display:none;">Envoyer</button>
                        </form>

                    </div>
                </div>
            <?php endforeach;?>

    </div>

</section>


<?php require_once 'app/views/footer.php';?>

<script src="assets/scripts/open_media.js"></script>
<script src="assets/scripts/add_media.js"></script>
<script src="assets/scripts/delete_media.js"></script>


</body>
</html>
