<?php
    $activeCategory[4] = true;
    $pageTitle = 'Změna stavu projekce';
    @require_once 'inc/user.php';

    if (@$_SESSION['user_id'] == null) {
        include 'inc/header.php';
        echo '<div class="mb-3">';
        echo '<div class="alert alert-danger" role="alert">Na tuto stránku mají přístup pouze přihlášení uživatelé!</div>';
        echo '<a href="index.php" class="btn btn-primary"><i class="bi bi-house-fill"></i> Zpět na domovskou stránku</a>';
        echo '</div>';
        include 'inc/footer.php';
        exit();
    } else {
        if (@$_SESSION['is_admin'] == false) {
            include 'inc/header.php';
            echo '<div class="mb-3">';
            echo '<div class="alert alert-danger" role="alert">Na tuto stránku mají přístup administrátoři!</div>';
            echo '<a href="index.php" class="btn btn-primary"><i class="bi bi-house-fill"></i> Zpět na domovskou stránku</a>';
            echo '</div>';
            include 'inc/footer.php';
            exit();
        }
}

    if (@$_GET['id'] AND ($_GET['new-status'] == 0 OR $_GET['new-status'] == 1)) {
        $updatePasswordQuery = $db->prepare('UPDATE movies SET is_projected = :is_projected WHERE movie_id = :movie_id;');
        $updatePasswordQuery->execute([
            'is_projected'=>(int) $_GET['new-status'],
            'movie_id'=>(int) $_GET['id']
        ]);

        header('Location: movies.php');
        exit();

    } else {
        include 'inc/header.php';
        echo '<div class="alert alert-warning" role="alert">Nebyly zadány parametry pro úravu stavu filmu.</div>';
        echo '<a href="movies.php" class="btn btn-primary">Zpět na seznam filmů</a>';
        include 'inc/footer.php';
    }
?>