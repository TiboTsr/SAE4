<?php
require_once 'app/models/files_save.php';
require_once 'app/models/galleryModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mediaid'], $_POST['eventid'])) {
    $fileName = getMedia($_POST['mediaid'], $_POST['eventid']);

    if(deleteFile($fileName)){
        // Met à jour la base de données avec le nom du fichier
        deleteMedia($_POST['mediaid'], $_POST['eventid']);
    }
        
    // Recharge la page pour afficher la nouvelle image
    header("Location: index.php?page=my_gallery&eventid=".$_POST["eventid"]);
    exit();

}else{
    header("Location: index.php");
    exit();
}

