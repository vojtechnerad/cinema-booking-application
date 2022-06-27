<?php
    $activeCategory[3] = true;
    $pageTitle = 'Editace kategorie';
    include 'inc/header.php';
    require_once 'inc/db.php';


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
    $id = $_GET['id'];
?>

<a href="categories.php">Zpět na výpis kategorií</a>
<h1>Editace kategorie</h1>
<?php
    if (!empty($id)) {
        if (is_numeric($id)) {
            $id = (int) $id;
            $categoryQuery = $db->prepare('SELECT * FROM categories WHERE category_id=:id LIMIT 1;');
            $categoryQuery->execute([
                ':id'=>$id
            ]);
            $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);
            if (!$category) {
                echo '<div class="alert alert-warning" role="alert">Hledaná kategorie se nenachází v databázi.</div>';
            } else {
                echo '<form method="post" action="update-category.php">';
                echo '<div class="mb-3">';
                echo '<input type="text" class="form-control" name="category-name" value="'. htmlspecialchars($category['name']) .'">';
                echo '<input type="hidden" name="category-id" value="' . $category['category_id'] . '">';
                echo '</div>';
                echo '<button type="submit" class="btn btn-primary"><i class="bi bi-save2-fill"></i> Uložit změnu</button>';
                echo '<a href="categories.php" class="btn btn-secondary">Zpět na výpis kategorií</a>';
                echo '</form>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Špatný formát ID kategorie!</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Nebyla zadána kategorie k úpravě!</div>';
    }
    include 'inc/footer.php'
?>