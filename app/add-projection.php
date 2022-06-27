<?php
    $activeCategory[3] = true;
    $pageTitle = 'Přidat projekci';
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

    if ($_POST) {
        $errorCount = 0;
        if (@$_POST['movie-id'] == null) {
            $errorCount++;
            $error['movie-id'] = true;
        } else {
            $error['movie-id'] = false;
        }

        if (@$_POST['date'] == '') {
            $errorCount++;
            $error['date'] = true;
        } else {
            $error['date'] = false;
        }

        if (@$_POST['time'] == '') {
            $errorCount++;
            $error['time'] = true;
        } else {
            $error['time'] = false;
        }

        if ($errorCount == 0) {
            $insertMovieQuery = $db->prepare('INSERT INTO projections (movie_id, time) VALUES (:movie_id, :dateandtime);');
            $insertMovieQuery->execute([
                ':movie_id'=>$_POST['movie-id'],
                'dateandtime'=> $_POST['date'] . ' ' . $_POST['time'] . ':00'
            ]);
            echo '<div class="alert alert-success" role="alert">Záznam byl úspěšně přidán.</div>';
        }
    }

    $activeMovies = $db->query('SELECT * FROM movies WHERE is_projected = TRUE')->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Přidání nové projekce</h1>

<form method="post">
    <div class="input-group mb-3">
        <label class="input-group-text" for="movie-id">Film</label>
        <select class="form-select" id="movie-id" name="movie-id">
            <?php
                if (@$_POST['movie-id']) {
                    echo '<option disabled>Vyberte</option>';
                    foreach ($activeMovies as $activeMovie) {
                        echo '<option value="' . $activeMovie['movie_id'] . '">' . htmlspecialchars($activeMovie['czech_name']) . '</option>';
                    }
                } else {
                    echo '<option selected disabled>Vyberte</option>';
                    foreach ($activeMovies as $activeMovie) {
                        echo '<option value="' . $activeMovie['movie_id'] . (($activeMovie['movie_id'] == $_POST['movie-id']) ? "selected" : "") . '">' . htmlspecialchars($activeMovie['czech_name']) . '</option>';
                    }
                }
            ?>
        </select>
    </div>
    <?php
    if (@$error['movie-id']) {
        echo '<p style="color:#DC3545;">Zvolte film <i class="bi bi-exclamation-circle"></i></p>';
    }
    ?>
    <div class="mb-3">
        <label for="date" class="form-label">Datum promítání</label>
        <input class="form-control" type="date" id="date" name="date" value="<?php echo @$_POST['date'];?>">
    </div>
    <?php
    if (@$error['date']) {
        echo '<p style="color:#DC3545;">Zvolte datum <i class="bi bi-exclamation-circle"></i></p>';
    }
    ?>
    <div class="mb-3">
        <label for="time" class="form-label">Čas promítání</label>
        <input type="time" class="form-control" id="time" name="time" value="<?php echo @$_POST['time'];?>">
    </div>
    <?php
    if (@$error['time']) {
        echo '<p style="color:#DC3545;">Zvolte čas <i class="bi bi-exclamation-circle"></i></p>';
    }
    ?>
    <button type="submit" class="btn btn-primary">Přidat novou projekci</button>
    <a href="projections.php" class="btn btn-secondary">Zpět na výpis projekcí</a>
</form>
<?php
    include 'inc/footer.php';
?>
