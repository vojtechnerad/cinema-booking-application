<?php

    $activeCategory[3] = true;
    $pageTitle = 'Administrace kategorií';
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

    function searchForMovieName($id, $array) {
        foreach ($array as $movie) {
            if ($movie['movie_id'] == $id) {
                return $movie['czech_name'];
            }
        }
    }
?>
<h1>Administrace projekcí</h1>
<a href="add-projection.php" class="btn btn-primary"><i class="bi bi-plus"></i> Přidat projekci</a>
<?php
    $projections = $db->query('SELECT * FROM projections;')->fetchAll(PDO::FETCH_ASSOC);
    $movies = $db->query('SELECT movie_id, czech_name FROM movies;')->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($projections)) {
        echo '<table class="table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">Promítaný film</th>';
        echo '<th scope="col">Čas promítání</th>';
        echo '<th scope="col">Editovat projekci</th>';
        echo '<th scope="col">Smazat projekci</th>';
        echo '</tr>';
        echo '<tbody>';
        foreach ($projections as $projection) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars(searchForMovieName($projection['movie_id'], $movies)) . '</td>'; //TODO PŘIDAT LINKY
            echo '<td>' . $projection['time'] . '</td>';
            echo '<td><a class="btn btn-primary" href="edit-projection.php?id='.$projection['projection_id'].'" role="button"><i class="bi bi-pencil-fill"></i> Editovat</a></td>';
            echo '<td><a class="btn btn-danger" href="delete-projection.php?id='.$projection['projection_id'].'" role="button"><i class="bi bi-trash-fill"></i> Smazat</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    include 'inc/footer.php';
?>
