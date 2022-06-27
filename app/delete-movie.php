<?php
    $activeCategory[3] = true;
    $pageTitle = 'Smazání fílmu';
    @require_once 'inc/user.php';
    include 'inc/header.php';

    if (@$_SESSION['user_id'] == null) {
        echo '<div class="mb-3">';
        echo '<div class="alert alert-danger" role="alert">Na tuto stránku mají přístup pouze přihlášení uživatelé!</div>';
        echo '<a href="index.php" class="btn btn-primary"><i class="bi bi-house-fill"></i> Zpět na domovskou stránku</a>';
        echo '</div>';
        include 'inc/footer.php';
        exit();
    } else {
        if (@$_SESSION['is_admin'] == false) {
            echo '<div class="mb-3">';
            echo '<div class="alert alert-danger" role="alert">Na tuto stránku mají přístup administrátoři!</div>';
            echo '<a href="index.php" class="btn btn-primary"><i class="bi bi-house-fill"></i> Zpět na domovskou stránku</a>';
            echo '</div>';
            include 'inc/footer.php';
            exit();
        }
}

    try {
        if ($_GET['id']) {
            $isMovieProjectedQuery = $db->prepare('SELECT * FROM projections WHERE movie_id = :id;');
            $isMovieProjectedQuery->execute([
                'id'=>$_GET['id']
            ]);
            $isMovieProjected = $isMovieProjectedQuery->fetchAll(PDO::FETCH_ASSOC);
            if ($isMovieProjected) {
                include 'inc/header.php';
                echo '<div class="alert alert-danger" role="alert">Chyba při mazání filmu!<br>Film je používán v databázi - existují jeho naplánované projekce.</div>';
                echo '<a href="movies.php" class="btn btn-primary">Zpět na výpis filmů</a>';
                include 'inc/footer.php';
            } else {
                $deleteCategoryQuery = $db->prepare('DELETE FROM movies WHERE movie_id=:id;');
                $deleteCategoryQuery->execute([
                    'id'=>$_GET['id']
                ]);
                header('Location: categories.php');
                exit();
            }

        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Chyba při mazání filmu!<br>Film je používán v databázi - existují jeho naplánované projekce.</div>';
        echo '<a href="movies.php" class="btn btn-primary">Zpět na výpis filmů</a>';
        include 'inc/footer.php';
    }


?>