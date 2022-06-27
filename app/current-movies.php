<?php
    $activeCategory[2] = true;
    $pageTitle = 'Promítané filmy';
    include 'inc/header.php';
    @$selectedCategory = $_GET['category-id'];
    $categories = $db->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Promítané filmy</h1>
<form method="get" action="current-movies.php">
    <div class="mb-3">
        <p>Filtrace podle kategorie</p>
        <select class="form-select" aria-label="Default select example" name="category-id">
            <?php
                echo '<option value="">Všechny</option>';

                foreach ($categories as $category) {
                    echo '<option value="' . $category['category_id'].'" ' . (((int) $selectedCategory == (int) $category['category_id'])?'selected':'') . '>' . htmlspecialchars($category['name']) . '</option>';
                }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filtrovat</button>
</form>
<?php
    if (empty($selectedCategory)) {
        $movies = $db->query('SELECT * FROM movies WHERE is_projected = TRUE ORDER BY release_date DESC;')->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $categoryIds = array_column($categories, 'category_id');
        if(in_array($selectedCategory, $categoryIds)) {
            $moviesQuery = $db->prepare('SELECT * FROM movies LEFT JOIN movie_categories USING (movie_id) WHERE is_projected = TRUE AND category_id = :category_id;');
            $moviesQuery->execute([
                'category_id'=>$selectedCategory
            ]);
            $movies = $moviesQuery->fetchAll(PDO::FETCH_ASSOC);
            if (!$movies) {
                echo '<div class="alert alert-warning" role="alert">Ve zvolené kategorii se nenachází žádný z právě promítaných filmů.</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Špatně zadaná kategorie</div>';
        }
    }

    if(!empty($movies)) {

        foreach ($movies as $movie) {
            echo '<div class="card mb-3">';
            echo '<div class="row g-0">';
            echo '<div class="col-md-3">';
            echo '<img src="posters/' . $movie['movie_id'] . '.jpg" class="img-fluid rounded-start" alt="...">';
            echo '</div>';
            echo '<div class="col-md-8">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">'. htmlspecialchars($movie['czech_name']) . '</h5>';
            echo '<p class="card-text"><small class="text-muted">'. htmlspecialchars($movie['original_name']) . '</small></p>';
            echo '<div class="row g-0">';
            echo '<div class="col-md-2">';
            echo '<p class="card-text"><i class="bi bi-calendar-event"></i> '. $movie['release_date'] . '</p>';
            echo '</div>';
            echo '<div class="col-md-2">';
            echo '<p class="card-text"><i class="bi bi-clock"></i> '. $movie['length'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '<p class="card-text fw-bold">Režisér</p>';
            echo '<p class="card-text">'. htmlspecialchars($movie['director']) . '</p>';
            echo '<p class="card-text fw-bold">Hrají</p>';
            echo '<p class="card-text">'. htmlspecialchars($movie['cast']) . '</p>';
            echo '<p class="card-text fw-bold">Kategorie</p>';
            $movieCategoriesQuery = $db->prepare('SELECT * FROM movie_categories LEFT JOIN categories USING (category_id) WHERE movie_id = :movie_id;');
            $movieCategoriesQuery->execute([
                    'movie_id'=>$movie['movie_id']
            ]);
            $movieCategories = $movieCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

            foreach ($movieCategories as $category) {
                echo htmlspecialchars($category['name']);
                if (next($movieCategories)) {
                    echo ', ';
                }
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }
    ?>
<?php
    include 'inc/footer.php';
?>
