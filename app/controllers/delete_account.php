<?php

require 'app/models/userModel.php';

if (isset($_POST['delete_account_valid']) && $_POST['delete_account_valid'] === 'true'){
    deleteUser();
    session_destroy();
    header("Location: index.php");
    exit();
}

require 'app/views/delete_account.php';