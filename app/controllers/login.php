<?php

require_once 'app/models/userModel.php';

function sanitizeLoginRedirect(?string $candidate): ?string
{
    if (!is_string($candidate)) {
        return null;
    }

    $candidate = trim($candidate);
    if ($candidate === '') {
        return null;
    }

    if (str_contains($candidate, "\r") || str_contains($candidate, "\n")) {
        return null;
    }

    if (str_contains($candidate, '://') || str_starts_with($candidate, '//')) {
        return null;
    }

    if (!str_starts_with($candidate, 'index.php')) {
        return null;
    }

    return $candidate;
}

$redirectCandidate = $_GET['next'] ?? $_POST['next'] ?? null;
$sanitizedRedirect = sanitizeLoginRedirect($redirectCandidate);
if ($sanitizedRedirect !== null) {
    $_SESSION['login_redirect'] = $sanitizedRedirect;
}

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

            $redirectTarget = $_SESSION['login_redirect'] ?? 'index.php';
            unset($_SESSION['login_redirect']);

            header("Location: " . $redirectTarget);
            exit;

        }else{
            echo $login_error;
        }

    }else{
        echo $login_error;
    }
}

$loginNext = $_SESSION['login_redirect'] ?? '';

require_once 'app/views/login.php';
        