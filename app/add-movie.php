<?php
    include 'inc/header.php';
    @require_once 'inc/user.php';
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

    if (!empty($_POST)) {
        //Získání ID, které nový film dostane
        $lastMovieIdQuery = $db->query('SHOW TABLE STATUS WHERE `Name` = "movies";');
        $lastMovieId = $lastMovieIdQuery->fetch(PDO::FETCH_ASSOC);
        $movieId = $lastMovieId['Auto_increment'];

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
            $length = $length . ':00';
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
                if(move_uploaded_file($imagetemp, $imagePath . $movieId . '.jpg')) {
                    echo "Sussecfully uploaded your image.";
                }
                else {
                    echo "Failed to move your image.";
                }
            }
            else {
                echo "Failed to upload your image.";
            }


            $insertMovieQuery = $db->prepare('INSERT INTO movies (czech_name, original_name, release_date, length, cast, director, is_projected) VALUES (:czechname, :originalname, :releasedate, :runningtime, :actors, :director, :isprojected);');
            $insertMovieQuery->execute([
                'czechname'=>$czechName,
                'originalname'=>$originalName,
                'releasedate'=>$releaseDate,
                'runningtime'=>$length,
                'director'=>$director,
                'actors'=>$cast,
                'isprojected'=>$isProjected
            ]);

            if ($_POST['categories']) {
                $categories = $_POST['categories'];
                foreach ($categories as $category) {

                    $movieCategoryQuery = $db->prepare('INSERT INTO movie_categories (movie_id, category_id) VALUES (:movie_id, :category_id);');
                    $movieCategoryQuery->execute([
                        'movie_id'=>$movieId,
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
<h1>Přidání nového filmu</h1>
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
            echo '<input type="text" class="form-control'.$state.'" id="czech-name" name="czech-name" value="'.htmlspecialchars(@$_POST['czech-name']).'">';
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
            echo '<input type="text" class="form-control'.$state.'" id="original-name" name="original-name" value="'.htmlspecialchars(@$_POST['original-name']).'">';
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
            echo '<input type="date" class="form-control'.$state.'" id="release-date" name="release-date" value="'.htmlspecialchars(@$_POST['release-date']).'">';
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
            echo '<input type="time" class="form-control'.$state.'" id="length" name="length" value="'.htmlspecialchars(@$_POST['length']).'">';
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
        echo '<input type="text" class="form-control'.$state.'" id="director" name="director" value="'.htmlspecialchars(@$_POST['director']).'">';
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
            echo '<input type="text" class="form-control'.$state.'" id="cast" name="cast" value="'.htmlspecialchars(@$_POST['cast']).'">';
        ?>
    </div>
    <div class="mb-3">
        <p>Žánry</p>
        <?php
            $categories = $db->query('SELECT * FROM categories;')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categories as $category) {
                echo '<div class="form-check">';
                echo '<input class="form-check-input" type="checkbox" value="'.$category['category_id'].'" id="category-'.$category['category_id'].'" name="categories[]" '.@((in_array($category['category_id'], $_POST['categories'])) ? " checked" : "").'>';
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
        <label class="form-check-label" for="is-projected">Film je připraven k projekcím</label>
    </div>
    <div class="mb-3">
        <label for="formFile" class="form-label">Plakát filmu (pouze .jpg!)</label>
        <input class="form-control" type="file" id="formFile" name="poster">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<?php
    include 'inc/footer.php';
?>
