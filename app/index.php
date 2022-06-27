<?php
    $activeCategory[1] = true;
    $pageTitle = 'Program projekcí';
    include 'inc/header.php';
    @require_once 'inc/user.php';

$seatCount = $db->query('SELECT count(seat_id) as "seat_count" FROM seats ;')->fetch(PDO::FETCH_ASSOC);
?>
<h1>Program představení</h1>
<?php
    $projections = $db->query('SELECT * FROM projections LEFT JOIN movies USING (movie_id) WHERE time > NOW() ORDER BY time;')->fetchAll(PDO::FETCH_ASSOC);

    foreach ($projections as $projection) {
        echo '<div class="card mb-3">';
        echo '<div class="row g-0">';
        echo '<div class="col-md-3">';
        echo '<img src="posters/' . $projection['movie_id'] . '.jpg" class="img-fluid rounded-start" alt="...">';
        echo '</div>';
        echo '<div class="col-md-8">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">'. htmlspecialchars($projection['czech_name']) . '</h5>';
        echo '<p class="card-text"><small class="text-muted">'. htmlspecialchars($projection['original_name']) . '</small></p>';
        $projection_time = $projection['time'];
        $projection_time = date_parse_from_format('Y-m-d h:i:s', $projection_time);

        echo '<p class="card-text"><i class="bi bi-calendar-event"></i> '. $projection_time['day'] . '. '. $projection_time['month'] . '. ' . $projection_time['year'] . '</p>';
        echo '<p class="card-text"><i class="bi bi-clock"></i> '. $projection_time['hour'] . ':'. $projection_time['minute'] .'</p>';


        $bookedSeatsQuery = $db->prepare('SELECT count(booking_id) as booked_seats FROM booked_tickets LEFT JOIN bookings USING(booking_id) where projection_id = :projection_id');
        $bookedSeatsQuery->execute([
            'projection_id'=>$projection['projection_id']
        ]);
        $bookedSeats = $bookedSeatsQuery->fetch(PDO::FETCH_ASSOC);
        $booked_percentage = round(((int) $bookedSeats['booked_seats'] / (int) $seatCount['seat_count']) * 100);
        echo '<p class="card-text"><i class="bi bi-people-fill"></i> '.$booked_percentage.'% zaplněnost</p>';

        if (@$_SESSION['user_id']) {
            echo '<a href="buy-tickets.php?id=' . $projection['projection_id'] . '" class="btn btn-primary">Koupit lístky</a>';
        } else {
            echo '<a href="#" class="btn btn-outline-primary disabled" aria-disabled="true">Pro nákup lístku se přihlašte</a>';
        }

        if (@$_SESSION['is_admin']) {
            echo '<a href="projection-details.php?id=' . $projection['projection_id'] . '" class="btn btn-warning">Detaily projekce</a>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
?>

<?php
    include 'inc/footer.php';
?>