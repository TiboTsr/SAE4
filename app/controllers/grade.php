<?php

require_once 'app/models/gradeModel.php';
require_once 'app/models/files_save.php';

$isLoggedIn = isset($_SESSION['userid']);
$products   = getGrades();

$gradesDisplay = [];
foreach ($products as $product) {
    $ownsGrade = $isLoggedIn && userOwnsGrade($_SESSION['userid'], $product['id_grade']);
    $gradesDisplay[] = [
        'data'      => $product,
        'ownsGrade' => $ownsGrade,
    ];
}


require_once 'app/views/grade.php';