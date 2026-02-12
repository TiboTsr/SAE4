<?php

require_once 'app/models/eventsModel.php';
require_once 'app/models/galleryModel.php';

$isLoggedIn = isset($_SESSION["userid"]);
$limit = 10;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET["show"]) && ctype_digit($_GET["show"])) {
        $limit = (int) $_GET["show"];
    }

    if(isset($_GET['eventid']) && $isLoggedIn){
        $eventid = $_GET['eventid'];
        $userid = $_SESSION["userid"];

    }else {
        header("Location: index.php");
        exit;
    }
}

$event = getTitle($eventid);

$medias = getMedias($userid, $eventid, $limit);

require 'app/views/my_gallery.php';