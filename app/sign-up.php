<?php
    $activeCategory[4] = true;
    $pageTitle = 'Registrace uživatele';
    require_once 'inc/user.php';
    include 'inc/header.php';

    $errorCount = 0;

    if (!empty($_POST)) {
        if (!empty($_POST['firstname'])) {
            $firstname = trim($_POST['firstname']);
        } else {
            $errorCount++;
            $error['firstname'] = TRUE;
        }

        if (!empty($_POST['lastname'])) {
            $lastname = trim($_POST['lastname']);
        } else {
            $errorCount++;
            $error['lastname'] = TRUE;
        }

        if (!empty($_POST['email'])) {
            $email = trim($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorCount++;
                $error['email'] = TRUE;
            } else {
                $emailQuery = $db->prepare('SELECT email FROM users WHERE email = :email LIMIT 1;');
                $emailQuery->execute([
                        'email'=>$email
                ]);
                $emailQuery = $emailQuery->fetchAll(PDO::FETCH_ASSOC);
                if ($emailQuery) {
                    $errorCount++;
                    $error['email_taken'] = TRUE;
                }
            }
        } else {
            $errorCount++;
            $error['email'] = TRUE;
        }

        if (empty($_POST['password']) || (strlen($_POST['password']) < 5)) {
            $errorCount++;
            $error['password'] = TRUE;
        } else {
            if ($_POST['password'] != $_POST['passwordRepeat']) {
                $errorCount++;
                $error['passwordRepeat'] = TRUE;
            } else {
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
        }

        if ($errorCount == 0) {
            $registerQuery = $db->prepare('INSERT INTO users (firstname, lastname, email, password_hash) VALUES (:firstname, :lastname, :email, :password_hash);');
            $registerQuery->execute([
                    ':firstname'=>$firstname,
                    ':lastname'=>$lastname,
                    ':email'=>$email,
                    ':password_hash'=>$password_hash
            ]);

            $userQuery = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1;');
            $userQuery->execute([
                'email'=>$email
            ]);
            $user = $userQuery->fetchAll(PDO::FETCH_ASSOC);
            if ($user) {
                $_SESSION['user_id'] = $user[0]['user_id'];
                $_SESSION['firstname'] = $user[0]['firstname'];
                $_SESSION['lastname'] = $user[0]['lastname'];
                header('Location: index.php');
                exit();
            }
        }
    }
?>
<h1>Registrace nového uživatele</h1>
<form method="post">
    <div class="row">
        <div class="col">
            <label for="firstname" class="form-label">Jméno</label>
            <?php
                $value = @$_POST['firstname'];
                $state = '';
                if ($_POST) {
                    if (@$error['firstname']) {
                        $state = ' is-invalid';
                    } else {
                        $state = ' is-valid';
                    }
                }
                echo '<input type="text" class="form-control' . $state . '" id="firstname" name="firstname" value="' . htmlspecialchars($value) . '">';
            ?>
        </div>
        <div class="col">
            <label for="lastname" class="form-label">Příjmení</label>
            <?php
                $value = @$_POST['lastname'];
                $state = '';
                if ($_POST) {
                    if (@$error['lastname']) {
                        $state = ' is-invalid';
                    } else {
                        $state = ' is-valid';
                    }
                }
                echo '<input type="text" class="form-control' . $state . '" id="lastname" name="lastname" value="' . htmlspecialchars($value) . '">';
            ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>

        <?php
            $value = @$_POST['email'];
            $state = '';
            if ($_POST) {
                if (@$error['email'] || @$error['email_taken']) {
                    $state = ' is-invalid';
                } else {
                    $state = ' is-valid';
                }
            }
            echo '<input type="text" class="form-control' . $state . '" id="email" aria-describedby="emailHelp" placeholder="example@domain.com" name="email" value="' . htmlspecialchars($value) . '">';
            if (@$error['email']) {
                echo '<div class="invalid-feedback" id="email">Email nemá správný formát.</div>';
            }
            if (@$error['email_taken']) {
                echo '<div class="invalid-feedback" id="email">Email již používá jiný účet.</div>';
            }
        ?>

    </div>
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
        <?php


        ?>
    </div>


    <button type="submit" class="btn btn-primary">Registrovat se</button>
    <a href="sign-in.php" class="btn btn-secondary">Zpět na přihlášení</a>
</form>
<?php
    include 'inc/footer.php';
?>

