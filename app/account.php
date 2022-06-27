<?
    @require_once 'inc/user.php';
    $accountQuery = $db->prepare('SELECT * FROM users WHERE user_id = :user_id LIMIT 1;');
    $accountQuery->execute([
        'user_id'=>$_SESSION['user_id']
    ]);
    $account = $accountQuery->fetchAll(PDO::FETCH_ASSOC);
    $activeCategory[4] = true;
    $pageTitle = $account[0]['firstname'] . ' ' . $account[0]['lastname'];

    include 'inc/header.php';
    if (@$_SESSION['user_id'] == null) {
        echo '<div class="mb-3">';
        echo '<div class="alert alert-danger" role="alert">Na tuto stránku mají přístup pouze přihlášení uživatelé!</div>';
        echo '<a href="index.php" class="btn btn-primary"><i class="bi bi-house-fill"></i> Zpět na domovskou stránku</a>';
        echo '</div>';
        include 'inc/footer.php';
        exit();
    }

?>
<h1>Detaily účtu</h1>
<a href="sign-out.php" class="btn btn-danger">Odhlásit se</a>
<h3>Detaily účtu</h3>
<?php

?>
<table class="table">
    <tbody>
    <tr>
        <th scope="row">Jméno</th>
        <td><?php echo htmlspecialchars($account[0]['firstname']) . ' ' . htmlspecialchars($account[0]['lastname'])?></td>
    </tr>
    <tr>
        <th scope="row">Email</th>
        <td><?php echo htmlspecialchars($account[0]['email'])?></td>
    </tr>
    <tr>
        <th scope="row">Heslo</th>
        <td><a href="change-password.php" class="btn btn-warning">Změnit heslo</a></td>
    </tr>
    </tbody>
</table>
<h3>Dokončené objednávky</h3>
<?php
    $bookingsQuery = $db->prepare('SELECT * FROM bookings WHERE user_id = :user_id;');
    $bookingsQuery->execute([
            'user_id'=>$_SESSION['user_id']
    ]);
    $bookings = $bookingsQuery->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bookings as $booking) {
        echo '<div class="card mb-3">';
        echo '<div class="row g-0">';
        $projectionQuery = $db->prepare('SELECT * FROM projections WHERE projection_id = :projection_id LIMIT 1;');
        $projectionQuery->execute([
            'projection_id'=>$booking['projection_id']
        ]);
        $projection = $projectionQuery->fetchAll(PDO::FETCH_ASSOC);

        $movieQuery = $db->prepare('SELECT * FROM movies WHERE movie_id = :movie_id LIMIT 1;');
        $movieQuery->execute([
                'movie_id'=>$projection[0]['movie_id']
        ]);
        $movie = $movieQuery->fetchAll();
        echo '<div class="col-md-3">';
        echo '<img src="posters/' . $movie[0]['movie_id'] . '.jpg" class="img-fluid rounded-start" alt="plakát filmu">';
        echo '</div>';

        echo '<div class="col-md-8">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">'. htmlspecialchars($movie[0]['czech_name']) . '</h5>';
        echo '<p class="card-text"><small class="text-muted">'. htmlspecialchars($movie[0]['original_name']) . '</small></p>';
        echo '<p class="card-text"><i class="bi bi-calendar-event"></i> '. $projection[0]['time'] . '</p>';

        $bookedTicketsQuery = $db->prepare('SELECT * FROM booked_tickets WHERE booking_id = :booking_id;');
        $bookedTicketsQuery->execute([
            'booking_id'=>$booking['booking_id']
        ]);
        $bookedTickets = $bookedTicketsQuery->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($bookedTickets) != 1) {
            echo '<p class="card-text fw-bold">Lístky</p>';
        } else {
            echo '<p class="card-text fw-bold">Lístek</p>';
        }

        $totalCost = 0;
        foreach ($bookedTickets as $bookedTicket) {
            $seatsQuery = $db->prepare('SELECT * FROM seats WHERE seat_id = :seat_id;');
            $seatsQuery->execute([
                'seat_id'=>$bookedTicket['seat_id']
            ]);
            $seat = $seatsQuery->fetchAll(PDO::FETCH_ASSOC);



            $ticketCategoryQuery = $db->prepare('SELECT * FROM ticket_categories WHERE ticket_category_id = :ticket_category_id;');
            $ticketCategoryQuery->execute([
                'ticket_category_id'=>$bookedTicket['ticket_category_id']
            ]);
            $ticketCategory = $ticketCategoryQuery->fetchAll(PDO::FETCH_ASSOC);
            echo '<p><i class="bi bi-ticket-fill"></i> Sedadlo ' . $seat[0]['seat_id'] . ' <em>(' . $seat[0]['seat_row'] . '; ' . $seat[0]['seat_column'] . ')</em> [' . $ticketCategory[0]['name'] .' - '.$ticketCategory[0]['price'].'Kč]</p>';
            $totalCost = $totalCost + (int) $ticketCategory[0]['price'];

        }
        echo '<p class="card-text fw-bold">Cena</p>';
        echo '<p class="card-text"><i class="bi bi-cash-coin"></i></i> '.$totalCost.' Kč</p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    include 'inc/footer.php';
?>
