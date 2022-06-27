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
    }

    $selectedSeats = $_POST;
    $ticketCategories = $db->query('SELECT * FROM ticket_categories')->fetchAll(PDO::FETCH_ASSOC);
?>
<a href="index.php">&#8592; zpět na program projekcí</a>
<h1>Výběr typu lístků</h1>
<form method="post" action="insert-tickets.php?id=<?php echo $_GET['id']?>">
<?php
    if (!empty($_POST)) {
        foreach ($selectedSeats as $selectedSeat) {
            echo '<h3>Sedadlo ' . $selectedSeat . '</h3>';
            echo '<select class="form-select" aria-label="Default select example" name="'. $selectedSeat .'">';
            foreach ($ticketCategories as $ticketCategory) {
                echo '<option value="'. $selectedSeat . '=' . $ticketCategory['ticket_category_id'] . '">' . $ticketCategory['name']. ' - ' . $ticketCategory['price'] . ' Kč</option>';
            }
            echo '</select>';
        }
    }
?>
    <input type="submit" value="Zaplatit" class="btn btn-primary">
</form>
<?php
    include 'inc/footer.php';
?>
