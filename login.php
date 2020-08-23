<?php

session_start();

require_once ('helpers.php');
require_once ('validation.php');
require_once('db.php');

// if user is already logged in
if (!empty($_SESSION)) {
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rules = [
        'email' => validateEmail($_POST['email']),
        'password' => validateSize($_POST['password'], 30)
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $errors[$key] = $rules[$key];
        }
    }
    $errors = array_filter($errors);

    // compare email with DB values, if exists compare password hash
    if (empty($errors)) {
        $stmt = $con->prepare("SELECT id, username, password FROM user WHERE email = ?");
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $stmt_result = mysqli_stmt_get_result($stmt);
        $record = mysqli_fetch_all($stmt_result, MYSQLI_ASSOC);

        if (mysqli_num_rows($stmt_result)) {
            if (password_verify($_POST['password'], $record[0]['password'])) {
                $user = array('email' => $_POST['email'], 'name' => $record[0]['username'], 'user_id' => $record[0]['id']);
                session_start();
                $_SESSION['user'] = $user;
//                var_dump($_SESSION['user']);
                header('Location: index.php');
            } else {
                $errors['password'] = "Incorrect password";
            }
        } else {
            $errors['email'] = "A user with this email is not found";
        }
        $stmt->close();
    }
}

$content = include_template('login_templ.php',
            [
                'errors' => $errors
            ]);

print($content);
