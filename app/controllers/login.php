<?php

require_once 'app/models/userModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
    $login_error = "<h3 class=\"login-error\">Erreur dans les informations de connexion.</h3>";
    $mail = htmlspecialchars(trim($_POST['mail']));
    $password = htmlspecialchars(trim($_POST['password']));

    $selection_db = getLogin($mail);

    if(!empty($selection_db)){

        $db_mail = $selection_db[0]["email_membre"];
        $db_password = $selection_db[0]["password_membre"];
        $mail_ok = ($db_mail == $mail);

        if($db_password == NULL && $password == ""){
            $password_ok = true;
        }else{
            $password_ok = password_verify($password, $db_password);
        }

        if($mail_ok && $password_ok){

            $_SESSION['userid'] = $selection_db[0]["id_membre"];

            //check if perm -> panel admin ok
            $result = isAdmin($_SESSION['userid']);

            $_SESSION["isAdmin"] = !empty($result);

            header("Location: index.php");
            exit;

        }else{
            echo $login_error;
        }

    }else{
        echo $login_error;
    }
}

require_once 'app/views/login.php';
        