<?php

require 'app/models/userModel.php';

function format_input($text){
    return htmlspecialchars(trim($text));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mail = htmlspecialchars(trim($_POST['mail']));

    $selection_db = getUserInfoByMail($mail);

    if(empty($selection_db)){

        $password = format_input($_POST['password']);
        $password_verif = format_input($_POST['password_verif']);

        if($password == $password_verif){
            $fname = "N/A";
            $lname = "N/A";
    
            if(isset($_POST['fname'])){
                $fname = format_input($_POST['fname']);
            }
            if(isset($_POST['lname'])){
                $lname = format_input($_POST['lname']);
            }

            insertUser($lname,$fname,$mail,$password);
        }
        header("Location: index.php?page=login");
        exit;

    }
}

require 'app/views/signin.php';