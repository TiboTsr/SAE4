<?php 
require_once '<app/models/eventsModel.php';
require_once '<app/models/galleryModel.php';

$show = 8;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $eventid = $_GET['id'];
    $event = getEvent($eventid);

    if(empty($event) || is_null($event)){
        header("Location: index.php");
        exit;
    }
    $event = $event[0];

    if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show']) {
        $show = (int) $_GET['show'];
    }

    $isLoggedIn = isset($_SESSION["userid"]);
    
    $current_date = new DateTime(date("Y-m-d"));
    $event_date = new DateTime(substr($event['date_evenement'], 0, 10));

    if($event_date >= $current_date){
        @$a = getInscription($_GET['id'], $_SESSION['userid']);
        $isSubscribed = !empty($a);
    }

    $medias = getMediaOrderBy($eventid, $show);
    
    $mediasLogin = getMediaOrderByWithLimit($eventid, $show);

    require 'app/views/event_details.php';

}else{
    header("Location: index.php");
    exit;
}
