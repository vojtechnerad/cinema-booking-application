<?php
    require_once 'inc/user.php';

    if (!empty($_SESSION['user_id'])) {
        unset($_SESSION['user_id']);
        unset($_SESSION['firstname']);
        unset($_SESSION['lastname']);
    }

    header('Location: index.php');