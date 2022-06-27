<?php
    $activeCategory[4] = true;
    $pageTitle = 'Změna hesla';
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

    if ($_POST) {
        if (strlen($_POST['password']) < 5) {
            $error['password'] = true;
        } else {
            if ($_POST['password'] != $_POST['passwordRepeat']) {
                $error['passwordRepeat'] = true;
            } else {
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $updatePasswordQuery = $db->prepare('UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id;');
                $updatePasswordQuery->execute([
                    'password_hash'=>$password_hash,
                    'user_id'=>$_SESSION['user_id']
                ]);
                echo '<div class="alert alert-success" role="alert">Heslo úspěšně změněno.</div>';
            }
        }
    }


?>
<h1>Změna hesla</h1>
<form method="post">
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="password" class="form-label">Heslo</label>
                <?php
                $state = '';
                if ($_POST) {
                    if (@$error['password']) {
                        $state = ' is-invalid';
                    } else {
                        $state = ' is-valid';
                    }
                }
                echo '<input type="password" class="form-control' .$state . '" id="password" name="password" aria-describedby="password">';

                if (@$error['password']) {
                    echo '<div class="invalid-feedback" id="password">Zadejte heslo alespoň 5 znaků dlouhé.</div>';

                }
                ?>
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="passwordRepeat" class="form-label">Heslo znovu</label>
                <?php
                    $state = '';
                    if ($_POST AND @$error['password'] == false) {
                        if (@$error['passwordRepeat']) {
                            $state = ' is-invalid';
                        } else {
                            $state = ' is-valid';
                        }
                    }
                    echo '<input type="password" class="form-control' .$state . '" id="passwordRepeat" name="passwordRepeat" aria-describedby="passwordRepeat">';

                    if (@$error['passwordRepeat']) {
                        echo '<div class="invalid-feedback" id="password">Hesla se neshodují.</div>';

                    }
                ?>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-warning">Změnit heslo</button>
    <a href="account.php" class="btn btn-secondary">Zrušit</a>
</form>
<?php
    include 'inc/footer.php';
?>
