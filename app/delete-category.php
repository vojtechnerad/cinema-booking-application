<?php
    require_once 'inc/user.php';

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


    try {
        if ($_GET['id']) {
            $deleteCategoryQuery = $db->prepare('DELETE FROM categories WHERE category_id=:id;');
            $deleteCategoryQuery->execute([
                'id'=>$_GET['id']
            ]);
            header('Location: movies.php');
            exit();
        }
    } catch (PDOException $e) {
        include 'inc/header.php';
        echo '<div class="alert alert-danger" role="alert">Chyba při mazání kategorie!<br>Kategorie je používána v databázi.</div>';
        echo '<a href="categories.php" class="btn btn-primary">Zpět na výpis kategorií</a>';
        include 'inc/footer.php';
    }