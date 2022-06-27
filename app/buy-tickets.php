<?php
    $activeCategory[1] = true;
    $pageTitle = 'Výběr sedadel';
    @require_once 'inc/user.php';
    if (@$_SESSION['user_id'] == null) {
        include 'inc/header.php';
        echo '<div class="mb-3">';
        echo '<div class="alert alert-danger" role="alert">Na tuto stránku mají přístup pouze přihlášení uživatelé!</div>';
        echo '<a href="index.php" class="btn btn-primary"><i class="bi bi-house-fill"></i> Zpět na domovskou stránku</a>';
        echo '</div>';
        include 'inc/footer.php';
        exit();
    }
    include 'inc/header.php';

?>
<script>
    function generateTicketOptions() {
        let checkboxes = document.querySelectorAll('.seat-checkbox');
        let formContainer = document.getElementById("form-container");

        while (formContainer.hasChildNodes()) {
            formContainer.removeChild(formContainer.firstChild);
        }
        let submitButton = document.createElement('input');
        submitButton.setAttribute('type', 'submit');
        submitButton.setAttribute('class', 'btn btn-primary');
        let numberOfCheckedSeats = 0;
        for (let i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                numberOfCheckedSeats++;
            }
        }

        if (numberOfCheckedSeats > 0) {
            submitButton.setAttribute('value', 'Objednat zvolené (' + numberOfCheckedSeats + ')');
            document.getElementById("form-container").appendChild(submitButton);
        } else {
            submitButton.setAttribute('value', 'Nelze objednat');
            submitButton.setAttribute('disabled', 'disabled');
            document.getElementById("form-container").appendChild(submitButton);
        }
    }
</script>
<a href="index.php">&#8592; zpět na program projekcí</a>
<?php

    $projectionQuery = $db->prepare('SELECT * FROM projections WHERE projection_id = :id;');
    $projectionQuery->execute([
        'id'=>$_GET['id']
    ]);

    $projection = $projectionQuery->fetch(PDO::FETCH_ASSOC);

    echo '<h1>Nákup lístků na projekci ' . $projection['projection_id'] . '</h1>';

    $seats = $db->query('SELECT * FROM seats')->fetchAll(PDO::FETCH_ASSOC);
    $seatStatusQuery = $db->prepare('SELECT * FROM booked_tickets LEFT JOIN bookings USING (booking_id) WHERE projection_id = :projection_id;');
    $seatStatusQuery->execute([
            'projection_id'=>$_GET['id']
    ]);
     $seatStatus = $seatStatusQuery->fetchAll(PDO::FETCH_ASSOC);
     $bookedSeats = array_column($seatStatus, 'seat_id');

    $maxX = max(array_column($seats, 'x_position'));
    $maxY = max(array_column($seats, 'y_position'));

    echo '<form method="post" action="choose-tickets.php?id='.$projection['projection_id'].'">';
    echo '<table class="table">';
    echo '<tbody>';
    for ($column = 1; $column <= $maxY; $column++) {
        echo '<tr>';
        for ($row = 1; $row <= $maxX; $row++) {
            $cellData = '- ulička -';
            foreach ($seats as $seat) {
                if ($seat['x_position'] == $row AND $seat['y_position'] == $column) {
                    if (in_array($seat['seat_id'], $bookedSeats)) {
                        $cellData = '
                        <div class="form-check">
                          <input class="form-check-input seat-checkbox" type="checkbox" value="'. $seat['seat_id'] .'" id="seat'. $seat['seat_id'] .'" name="'. $seat['seat_id'] .'" disabled>
                          <label class="form-check-label" for="seat'. $seat['seat_id'] .'"><s>'. $seat['seat_id'] . ' (' . $column . ';' . $row . ')</s></label>
                        </div>';
                    } else {
                        $cellData = '
                        <div class="form-check">
                          <input class="form-check-input seat-checkbox" type="checkbox" value="'. $seat['seat_id'] .'" id="seat'. $seat['seat_id'] .'" name="'. $seat['seat_id'] .'" onclick="generateTicketOptions();">
                          <label class="form-check-label" for="seat'. $seat['seat_id'] .'">'. $seat['seat_id'] . ' (' . $column . ';' . $row . ')</label>
                        </div>';
                    }
                }
            }
            echo '<td>' . $cellData . '</td>';

        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
?>
<div id="form-container">
</div>
<script>generateTicketOptions();</script>
<?php
    echo '</form>';
    include 'inc/footer.php';
?>
