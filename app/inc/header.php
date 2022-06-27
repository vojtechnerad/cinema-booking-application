<?php
    require_once 'user.php';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="">
    <meta name="author" content="Vojtěch Nerad">
    <title><?php echo @$pageTitle . ' &#8226; Generické kino'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="apple-touch-icon" sizes="180x180" href="inc/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="inc/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="inc/favicon/favicon-16x16.png">
    <link rel="manifest" href="inc/favicon/site.webmanifest">
</head>
<body>
<div class="container">
    <header>
    </header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="inc/icon.png" alt="" width="20"> Generické kino</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ((@$activeCategory[1]) ? ' active' : '') ?>" aria-current="page" href="index.php">Program projekcí</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ((@$activeCategory[2]) ? ' active' : '') ?>" href="current-movies.php">Promítané filmy</a>
                    </li>

                    <?php
                        if (@$_SESSION['user_id']) {
                            $isAdminQuery = $db->prepare('SELECT is_admin FROM users WHERE user_id = :user_id;');
                            $isAdminQuery->execute([
                                'user_id'=>$_SESSION['user_id']
                            ]);
                            $isAdmin = $isAdminQuery->fetch(PDO::FETCH_ASSOC);

                            if ($isAdmin['is_admin']) {
                                echo '<li class="nav-item dropdown">';
                                echo '<a class="nav-link dropdown-toggle' . ((@$activeCategory[3]) ? ' active' : '') . '" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                                echo '<i class="bi bi-gear-fill"></i> Administrace</a>';
                                echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                                echo '<li><a class="dropdown-item" href="movies.php">Seznam filmů</a></li>';
                                echo '<li><a class="dropdown-item" href="categories.php">Kategorie</a></li>';
                                echo '<li><hr class="dropdown-divider"></li>';
                                echo '<li><a class="dropdown-item" href="projections.php">Představení</a></li>';
                                echo '</ul>';
                                echo '</li>';
                            }
                        }
                    ?>

                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <?php

                        if (@$_SESSION['user_id']) {
                            echo '<a class="nav-link' . ((@$activeCategory[4]) ? ' active' : '') . '" href="account.php"><i class="bi bi-person-circle"></i></i>  ' . htmlspecialchars($_SESSION['firstname']) . ' ' . htmlspecialchars($_SESSION['lastname']). '</a>';
                        } else {
                            echo '<a class="nav-link ' . ((@$activeCategory[4]) ? ' active' : '') . '" href="./sign-in.php"><i class="bi bi-person-circle"></i></i> Přihlásit se</a>';
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>