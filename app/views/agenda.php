<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="assets/styles/planner_style.css">
    <link rel="stylesheet" href="assets/styles/general_style.css">
    <link rel="stylesheet" href="assets/styles/header_style.css">
    <link rel="stylesheet" href="assets/styles/footer_style.css">
    
</head>
<body class="body_margin">

<?php 

// Importer les fichiers
require_once "app/views/header.php" ;
require_once 'app/views/Database.php';
require_once 'app/views/files_save.php';
?>

<H1>Agenda</H1>
<div>
    <iframe src="https://edt.gemino.dev">
    </iframe>
</div>


<?php require_once "app/views/footer.php" ?>
</body>
</html>