<?php
    $activeCategory[3] = true;
    $pageTitle = 'Upravit projekci';
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

    if ($_GET['id']) {
        $projectionQuery = $db->prepare('SELECT * FROM projections WHERE projection_id = :id LIMIT 1;');
        $projectionQuery->execute([
            'id'=>(int) $_GET['id']
        ]);
        @$projection = $projectionQuery->fetchAll(PDO::FETCH_ASSOC);
        @$projection = $projection[0];
        if (@$projection != null) {

        } else {
            echo '<div class="alert alert-warning" role="alert">Daný záznam neexistuje!</div>';
            echo '<a href="projections.php" class="btn btn-primary">Zpět na projekce</a>';
        }
        $timestamp = $projection['time'];
        $splitTimestamp = explode(' ', $timestamp);
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
            $updateMovieQuery = $db->prepare('UPDATE projections SET movie_id = :movie_id, time = :dateandtime WHERE projection_id = :id;');
            $updateMovieQuery->execute([
                ':movie_id'=> $_POST['movie-id'],
                'dateandtime'=> $_POST['date'] . ' ' . $_POST['time'],
                'id'=>$_GET['id']
            ]);
            echo '<div class="alert alert-success" role="alert">Záznam byl úspěšně změněn.</div>';
        }
    }
?>
<h1>Editace projekce</h1>

<form method="post">
    <div class="input-group mb-3">
        <label class="input-group-text" for="movie-id">Film</label>
        <select class="form-select" id="movie-id" name="movie-id">
            <?php
                $activeMovies = $db->query('SELECT * FROM movies WHERE is_projected = TRUE')->fetchAll(PDO::FETCH_ASSOC);
                if (@$_POST['movie-id'] != '') {

                    echo '<option disabled>Vyberte</option>';
                    foreach ($activeMovies as $activeMovie) {
                        echo '<option value="' . $activeMovie['movie_id'] . '" ' . htmlspecialchars(@(($activeMovie['movie_id'] == $_POST['movie-id']) ? "selected" : "")) . '>' . htmlspecialchars($activeMovie['czech_name']) . '</option>';
                    }
                } else {
                    echo '<option disabled>Vyberte</option>';
                    foreach ($activeMovies as $activeMovie) {
                        echo '<option value="' . $activeMovie['movie_id'] . '" ' . htmlspecialchars(@(($activeMovie['movie_id'] == $projection['movie_id']) ? "selected" : "")) . '>' . htmlspecialchars($activeMovie['czech_name']) . '</option>';
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

        <?php
        if ($_POST) {
            echo '<input class="form-control" type="date" id="date" name="date" value="'.  @$_POST['date'] .'">';
        } else {
            echo '<input class="form-control" type="date" id="date" name="date" value="'.  @$splitTimestamp[0] .'">';
        }
        ?>

    </div>
    <?php
    if (@$error['date']) {
        echo '<p style="color:#DC3545;">Zvolte datum <i class="bi bi-exclamation-circle"></i></p>';
    }
    ?>
    <div class="mb-3">
        <label for="time" class="form-label">Čas promítání</label>
        <?php
        if ($_POST) {
            echo '<input type="time" class="form-control" id="time" name="time" value="'. @$_POST['time'].'">';
        } else {
            echo '<input type="time" class="form-control" id="time" name="time" value="'. @$splitTimestamp[1].'">';
        }
        ?>

    </div>
    <?php
    if (@$error['time']) {
        echo '<p style="color:#DC3545;">Zvolte čas <i class="bi bi-exclamation-circle"></i></p>';
    }
    ?>
    <button type="submit" class="btn btn-primary">Uložit změny</button>
    <a href="projections.php" class="btn btn-secondary">Zpět na výpis projekcí</a>
</form>
<?php
    include 'inc/footer.php';