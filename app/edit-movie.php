<?php
    $activeCategory[3] = true;
    $pageTitle = 'Editace filmu';
    require_once 'inc/user.php';
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

    if (!$_GET['id']) {
        echo '<div class="alert alert-warning" role="alert">Nebyl specifikován film k editaci.</div>';
        echo '<a href="movies.php">Zpět na seznam filmů</a>';
        exit();
    }
    $movieDetailsQuery = $db->prepare('SELECT * FROM movies WHERE movie_id = :id;');
    $movieDetailsQuery->execute([
        'id'=>$_GET['id']
    ]);
    $movieDetails = $movieDetailsQuery->fetchAll(PDO::FETCH_ASSOC);
    $movieDetails = $movieDetails[0];
    if (!$movieDetails) {
        echo '<div class="alert alert-warning" role="alert">Specifikovaný film nebyl nalezen.</div>';
        echo '<a href="movies.php">Zpět na seznam filmů</a>';
        exit();
    }

    if ($_POST) {
        $errorCount = 0;
        $czechName = $_POST['czech-name'];
        if ($czechName == '') {
            $errors['czech-name'] = true;
            $errorCount++;
        } else {
            $errors['czech-name'] = false;
        }

        $originalName = $_POST['original-name'];
        if ($originalName == '') {
            $errors['original-name'] = true;
            $errorCount++;
        } else {
            $errors['original-name'] = false;
        }

        $releaseDate = $_POST['release-date'];
        if ($releaseDate == '') {
            $errors['release-date'] = true;
            $errorCount++;
        } else {
            $errors['release-date'] = false;
        }

        $length = $_POST['length'];
        if ($length == '') {
            $errors['length'] = true;
            $errorCount++;
        } else {
            $errors['length'] = false;
        }

        $director = $_POST['director'];
        if ($director == '') {
            $errors['director'] = true;
            $errorCount++;
        } else {
            $errors['director'] = false;
        }

        $cast = $_POST['cast'];
        if ($cast == '') {
            $errors['cast'] = true;
            $errorCount++;
        } else {
            $errors['cast'] = false;
        }

        if ($_POST['is-projected'] == 'on') {
            $isProjected = 1;
        } else {
            $isProjected = 0;
        }

        if (@$_POST['categories'] == '') {
            $errors['categories'] = true;
            $errorCount++;
        } else {
            $errors['categories'] = false;
        }

        if ($errorCount == 0) {
            $imagename = $_FILES['poster']['name'];
            $imagetype = $_FILES['poster']['type'];
            $imageerror = $_FILES['poster']['error'];
            $imagetemp = $_FILES['poster']['tmp_name'];
            $imagePath = "posters/";

            if(is_uploaded_file($imagetemp)) {
                if(move_uploaded_file($imagetemp, $imagePath . $movieDetails['movie_id'] . '.jpg')) {
                    echo "Sussecfully uploaded your image.";
                }
                else {
                    echo "Failed to move your image.";
                }
            }
            else {
                echo "Failed to upload your image.";
            }


            $updateMovieQuery = $db->prepare('UPDATE movies SET czech_name = :czechname, original_name = :originalname, release_date = :releasedate, length = :runningtime, cast = :actors, director = :director, is_projected = :isprojected WHERE movie_id = :id;');
            $updateMovieQuery->execute([
                'czechname'=>$czechName,
                'originalname'=>$originalName,
                'releasedate'=>$releaseDate,
                'runningtime'=>$length,
                'director'=>$director,
                'actors'=>$cast,
                'isprojected'=>$isProjected,
                'id'=>(int) $_GET['id']
            ]);

            $deleteCategoriesQuery = $db->prepare('DELETE FROM movie_categories WHERE movie_id = :id;');
            $deleteCategoriesQuery->execute([
               'id'=> (int) $_GET['id']
            ]);

            if ($_POST['categories']) {
                $categories = $_POST['categories'];
                foreach ($categories as $category) {

                    $movieCategoryQuery = $db->prepare('INSERT INTO movie_categories (movie_id, category_id) VALUES (:movie_id, :category_id);');
                    $movieCategoryQuery->execute([
                        'movie_id'=>(int) $_GET['id'],
                        'category_id'=>$category
                    ]);
                }
            }

            header('Location: movies.php');
            exit();
        }
    }

?>
<a href="movies.php">Zpět na seznam filmů</a>
<h1>Editace filmu</h1>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="czech-name" class="form-label">Český název</label>
        <?php
        $state = '';
        if ($_POST) {
            if (@$errors['czech-name']) {
                $state = ' is-invalid';
            } else {
                $state = ' is-valid';
            }
        }
        echo '<input type="text" class="form-control'.$state.'" id="czech-name" name="czech-name" value="'.htmlspecialchars(@(($_POST) ? $_POST['czech-name']: $movieDetails['czech_name'])).'">';

        ?>
    </div>
    <div class="mb-3">
        <label for="original-name" class="form-label">Originální název</label>
        <?php
        $state = '';
        if ($_POST) {
            if (@$errors['original-name']) {
                $state = ' is-invalid';
            } else {
                $state = ' is-valid';
            }
        }
        echo '<input type="text" class="form-control'.$state.'" id="original-name" name="original-name" value="'.htmlspecialchars(@(($_POST) ? $_POST['original-name']: $movieDetails['original_name'])).'">';
        ?>
    </div>
    <div class="mb-3">
        <label for="release-date" class="form-label">Datum premiéry</label>
        <?php
        $state = '';
        if ($_POST) {
            if (@$errors['release-date']) {
                $state = ' is-invalid';
            } else {
                $state = ' is-valid';
            }
        }
        echo '<input type="date" class="form-control'.$state.'" id="release-date" name="release-date" value="'.htmlspecialchars(@(($_POST) ? $_POST['release-date']: $movieDetails['release_date'])).'">';
        ?>
    </div>
    <div class="mb-3">
        <label for="length" class="form-label">Délka</label>
        <?php
        $state = '';
        if ($_POST) {
            if (@$errors['length']) {
                $state = ' is-invalid';
            } else {
                $state = ' is-valid';
            }
        }
        echo '<input type="time" class="form-control'.$state.'" id="length" name="length" value="'.@(($_POST) ? $_POST['length']: $movieDetails['length']).'">';
        ?>
    </div>
    <div class="mb-3">
        <label for="director" class="form-label">Režisér</label>
        <?php
        $state = '';
        if ($_POST) {
            if (@$errors['director']) {
                $state = ' is-invalid';
            } else {
                $state = ' is-valid';
            }
        }
        echo '<input type="text" class="form-control'.$state.'" id="director" name="director" value="'.htmlspecialchars(@(($_POST) ? $_POST['director']: $movieDetails['director'])).'">';
        ?>
    </div>


    <div class="mb-3">
        <label for="cast" class="form-label">Herci</label>
        <?php
        $state = '';
        if ($_POST) {
            if (@$errors['cast']) {
                $state = ' is-invalid';
            } else {
                $state = ' is-valid';
            }
        }
        echo '<input type="text" class="form-control'.$state.'" id="cast" name="cast" value="'.htmlspecialchars(@(($_POST) ? $_POST['cast']: $movieDetails['cast'])).'">';
        ?>
    </div>
    <div class="mb-3">
        <p>Žánry</p>
        <?php
            $categories = $db->query('SELECT * FROM categories;')->fetchAll(PDO::FETCH_ASSOC);
            $checkedCategories = $db->prepare('SELECT * FROM movie_categories WHERE movie_id = :id;');
            $checkedCategories->execute([
                'id'=>$_GET['id']
            ]);
            $checkedCategories = $checkedCategories->fetchAll(PDO::FETCH_ASSOC);
            $checkedCategories = array_column($checkedCategories, 'category_id');
            foreach ($categories as $category) {
                echo '<div class="form-check">';
                if ($_POST) {
                    echo '<input class="form-check-input" type="checkbox" value="'.$category['category_id'].'" id="category-'.$category['category_id'].'" name="categories[]" '.htmlspecialchars(@((in_array($category['category_id'], $_POST['categories']))) ? " checked" : "").'>';
                } else {
                    echo '<input class="form-check-input" type="checkbox" value="'.$category['category_id'].'" id="category-'.$category['category_id'].'" name="categories[]" '.htmlspecialchars(@((in_array($category['category_id'], $checkedCategories)) ? " checked" : "")).'>';
                }

                echo '<label class="form-check-label" for="category-'.$category['category_id'].'">'.htmlspecialchars($category['name']).'</label>';
                echo '</div>';
            }
            if (@$_POST AND @$_POST['categories'] == '') {
                echo '<p style="color:#DC3545;">Zvolte alespoň jednu kategorii <i class="bi bi-exclamation-circle"></i></p>';
            }
        ?>
    </div>

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" role="switch" id="is-projected" name="is-projected" checked>
        <label class="form-check-label" for="is_projected">Film je připraven k projekcím</label>
    </div>
    <div class="mb-3">
        <label for="formFile" class="form-label">Plakát filmu (pouze .jpg!)</label>
        <input class="form-control" type="file" id="formFile" name="poster">
    </div>
    <button type="submit" class="btn btn-primary">Uložit změny</button>
</form>
<?php
    include 'inc/footer.php';
?>

