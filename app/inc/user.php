<?php
  session_start();
  require_once 'db.php'; // Připojení k DB

    $userQuery=$db->prepare('SELECT is_admin FROM users WHERE user_id=:id LIMIT 1;');
    $userQuery->execute([
        ':id'=>@$_SESSION['user_id']
    ]);
    $user = $userQuery->fetchAll(PDO::FETCH_ASSOC);
    @$_SESSION['is_admin'] = $user[0]['is_admin'];
