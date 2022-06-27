<?php
    $activeCategory[3] = true;
    $pageTitle = 'Upravit projekci';
    include 'inc/header.php';
    @require_once 'inc/user.php';


    try {
        if ($_GET['id']) {
            $deleteProjectionQuery = $db->prepare('DELETE FROM projections WHERE projection_id=:id;');
            $deleteProjectionQuery->execute([
                'id'=>$_GET['id']
            ]);
            header('Location: projections.php');
            exit();

        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Chyba při mazání projekce!<br>Na projekci již byly prodány lístky!.</div>';
        echo '<a href="projections.php" class="btn btn-primary">Zpět na výpis projekcí</a>';
        include 'inc/footer.php';
    }