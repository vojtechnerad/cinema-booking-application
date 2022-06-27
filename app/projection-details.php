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
?>
<h1>Detaily projekce</h1>
<?php
    $seats = $db->query('SELECT * FROM seats;')->fetchAll(PDO::FETCH_ASSOC);
    $bookedSeats = $db->prepare('SELECT * FROM booked_tickets LEFT JOIN bookings USING(booking_id) WHERE projection_id = :projection_id;');

    $id = $_GET['id'];
    $bookedSeats->execute([
        'projection_id'=>$id
    ]);
    $bookedSeats = $bookedSeats->fetchAll(PDO::FETCH_ASSOC);
    $bookedSeatsData = $bookedSeats;
    $bookedSeats = array_column($bookedSeats, 'seat_id');;


    echo '<table class="table">';
    echo '<tbody>';
    foreach ($seats as $seat) {
        echo '<tr>';
        echo '<td>Sedalo '.$seat['seat_id'].'</td>';

        if (array_search($seat['seat_id'], $bookedSeats)) {

            foreach ($bookedSeatsData as $data) {
                if ($seat['seat_id'] == $data['seat_id']) {
                    $userName = $db->prepare('SELECT * FROM users WHERE user_id = :user_id;');
                    $userName->execute([
                        'user_id'=>$data['user_id']
                    ]);
                    $userName = $userName->fetchAll(PDO::FETCH_ASSOC);
                    $userName = $userName[0];
                    echo '<td>'.htmlspecialchars($userName['firstname']) . ' ' . htmlspecialchars($userName['lastname']). ' ['.htmlspecialchars($userName['email']) .']</td>';
                }
            }
        } else {
            echo '<td>'.'-'.'</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
?>