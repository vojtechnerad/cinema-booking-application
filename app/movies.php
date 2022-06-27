<?php
    require_once 'inc/user.php';
    $activeCategory[3] = true;
    $pageTitle = 'Seznam filmů';
    @include 'inc/header.php';

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



?>
<h1>Seznam filmů</h1>
<a href="add-movie.php" class="btn btn-primary">Přidat nový film</a>
<?php
    $movies = $db->query('SELECT * FROM movies;')->fetchAll(PDO::FETCH_ASSOC);

    if(!empty($movies)) {

        foreach ($movies as $movie) {
            echo '<div class="card mb-3">';
            echo '<div class="row g-0">';
            echo '<div class="col-md-3">';
            echo '<img src="posters/' . $movie['movie_id'] . '.jpg" class="img-fluid rounded-start" alt="Plakát filmu">';
            echo '</div>';
            echo '<div class="col-md-8">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">'. htmlspecialchars($movie['czech_name']) . '</h5>';
            echo '<p class="card-text"><small class="text-muted">'. htmlspecialchars($movie['original_name']) . '</small></p>';
            echo '<p class="card-text"><i class="bi bi-calendar-event"></i> '. $movie['release_date'] . '</p>';
            echo '<p class="card-text"><i class="bi bi-clock"></i> '. $movie['length'] . '</p>';
            echo '<p class="card-text fw-bold">Režisér</p>';
            echo '<p class="card-text">'. htmlspecialchars($movie['director']) . '</p>';
            echo '<p class="card-text fw-bold">Hrají</p>';
            echo '<p class="card-text">'. htmlspecialchars($movie['cast']). '</p>';
            $status = '';
            if ($movie['is_projected']) {
                $status = '<i class="bi bi-check-lg"></i> Film je připraven k projekci';
            } else {
                $status = '<i class="bi bi-x-lg"></i> Film není připraven k projekci';
            }
            echo '<p class="card-text">'. $status . '</p>';
            echo '<div class="row g-0">';
            echo '<div class="col-md-3">';
            echo '<a href="change-movie-status.php?id=' . $movie['movie_id'] . '&new-status=' . (($movie['is_projected']) ? "0" : "1").'" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Změňit stav projekce</a>';
            echo '</div>';
            echo '<div class="col-md-3">';
            echo '<a href="edit-movie.php?id='.$movie['movie_id'].'" class="btn btn-primary"><i class="bi bi-pencil-fill"></i> Editovat film</a>';
            echo '</div>';
            echo '<div class="col-md-3">';
            echo '<a href="delete-movie.php?id='.$movie['movie_id'].'" class="btn btn-danger"><i class="bi bi-trash-fill"></i> Smazat film</a>';
            echo '</div>';
            echo '</div>';

            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

    }

    include 'inc/footer.php';
?>
