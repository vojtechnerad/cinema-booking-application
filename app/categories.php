<?php
    require_once 'inc/db.php';
    $activeCategory[3] = true;
    $pageTitle = 'Administrace kategorií';
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
?>
<h1>Administrace kategorií</h1>
<h3>Přidání nové kategorie</h3>
<form method="post" action="add-category.php">
    <div class="mb-3">
        <label for="new-category" class="form-label">Název nové kategorie</label>
        <input type="text" class="form-control" id="new-category" name="category-name">
    </div>
    <button type="submit" class="btn btn-primary"><i class="bi bi-plus"></i> Přidat kategorii</button>
</form>
<h3>Editace stávajících kategorií</h3>
<?php
    $categories = $db->query('SELECT * FROM categories;')->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($categories)) {
        echo '<table class="table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">#</th>';
        echo '<th scope="col">Název kategorie</th>';
        echo '<th scope="col">Editovat kategorii</th>';
        echo '<th scope="col">Smazat kategorii</th>';
        echo '</tr>';
        echo '<tbody>';
        foreach ($categories as $category) {
            echo '<tr>';
            echo '<th scope="row">' . $category['category_id'] . '</th>';
            echo '<td>' . htmlspecialchars($category['name']) . '</td>';
            echo '<td><a class="btn btn-primary" href="edit-category.php?id=' . $category['category_id']. '" role="button"><i class="bi bi-pencil-fill"></i> Editovat</a></td>';
            echo '<td><a class="btn btn-danger" href="delete-category.php?id=' . $category['category_id']. '" role="button"><i class="bi bi-trash-fill"></i> Smazat</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    include 'inc/footer.php';
?>