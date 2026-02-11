<?php
require_once 'app/models/Database.php';

function getGrade() {
    $db = new Database();
    return $db->select("SELECT * FROM GRADE WHERE deleted = false ORDER BY prix_grade");
}