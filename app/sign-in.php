<?php
    $activeCategory[4] = true;
    $pageTitle = 'Přihlášení uživatele';
    require_once 'inc/db.php';
    include 'inc/header.php';

    if (!empty($_POST)) {
        $userVerificationQuery = $db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1;');
        $userVerificationQuery->execute([
                ':email'=>trim($_POST['email'])
        ]);

        $user = $userVerificationQuery->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (password_verify($_POST['password'], $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                header('Location: account.php');
                exit();
            }
        }
        echo '<div class="alert alert-warning" role="alert">Zadaná kombinace přihlašovacích údajů je neplatná.</div>';
    }
?>
<h1>Přihlášení uživatele</h1>

<form method="post">
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Email</label>
        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="email">
    </div>
    <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Heslo</label>
        <input type="password" class="form-control" id="exampleInputPassword1" name="password">
    </div>
    <button type="submit" class="btn btn-primary">Přihlásit se</button>
</form>

Nemáte účet? <a href="./sign-up.php">Zaregistrujte se</a>
<?php
include 'inc/footer.php';
?>
