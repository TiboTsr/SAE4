<?php

require 'app/models/userModel.php';


if (isset($_POST['delete_account_valid']) && $_POST['delete_account_valid'] === 'true') {
    deleteUser();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_account'])
    && $_POST['delete_account'] === 'true'
) {
    require 'app/views/delete_account.php';
} else {
    header("Location: index.php");
    exit();
}
