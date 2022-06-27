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
    }

    $lastBookingIdQuery = $db->query('SHOW TABLE STATUS WHERE `Name` = "bookings";');
    $lastBookingId = $lastBookingIdQuery->fetch(PDO::FETCH_ASSOC);
    $bookingId = $lastBookingId['Auto_increment'];

    $seatTaken = FALSE;
    foreach ($_POST as $ticket) {
        $ticketDetails = explode('=', $ticket);
        $seatId = $ticketDetails[0];
        $ticketCategoryId = $ticketDetails[1];
        var_dump($seatId);
        $seatStatusQuery = $db->prepare('SELECT * FROM booked_tickets LEFT JOIN bookings USING (booking_id) WHERE projection_id = :projection_id AND seat_id = :seat_id;');
        $seatStatusQuery->execute([
            'projection_id'=>$_GET['id'],
            'seat_id'=>$seatId
        ]);
        $seatStatus = $seatStatusQuery->fetchAll(PDO::FETCH_ASSOC);
        var_dump($seatStatus);
        if ($seatStatus != null) {
            $seatTaken = TRUE;
        }

    }

    if ($seatTaken == FALSE) {
        $addBooking = $db->prepare('INSERT INTO bookings (user_id, projection_id) VALUE (:user_id, :projection_id);');
        $addBooking->execute([
            'user_id'=>$_SESSION['user_id'],
            'projection_id'=>$_GET['id']
        ]);

        foreach ($_POST as $ticket) {
            $ticketDetails = explode('=', $ticket);
            $seatId = $ticketDetails[0];
            $ticketCategoryId = $ticketDetails[1];
            $seatStatusQuery = $db->prepare('SELECT * FROM booked_tickets LEFT JOIN bookings USING (booking_id) WHERE projection_id = :projection_id AND seat_id = :seat_id;');
            $seatStatusQuery->execute([
                'projection_id'=>$_GET['id'],
                'seat_id'=>$seatId
            ]);

            $addBookedTicket = $db->prepare('INSERT INTO booked_tickets (booking_id, ticket_category_id, seat_id) VALUES (:booking_id, :ticket_category_id, :seat_id);');
            $addBookedTicket->execute([
                'booking_id'=>$bookingId,
                'ticket_category_id'=>$ticketCategoryId,
                'seat_id'=>$seatId
            ]);

        }
        header('Location: account.php');
        exit();
    } else {
        include 'inc/header.php';
        echo '<div class="alert alert-danger" role="alert">Objednávka nebyla dokončena, místa zabral již někdo jiný.</div>';
        include 'inc/footer.php';
    }
