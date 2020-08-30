<?php

session_start();

require_once('helpers.php');
require_once('db.php');
require_once('validation.php');

// if user already logged in
if (UserHelper::isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = trim($_POST['email']);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $rules = [
        'email' => validateEmail($email),
        'password' => validatePassword($_POST['password']),
        'name' => validateSize($name)
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $errors[$key] = $rules[$key];
        }
    }

    // if user with this email already exists in DB
    $stmt = $con->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt_result = mysqli_stmt_get_result($stmt);
    $stmt->close();

    if (mysqli_num_rows($stmt_result)) {
        $errors['email'] = "This email is already registered";
    }

    $errors = array_filter($errors);

    if (empty($errors)) {
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $passwordHash);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header('Location: login.php');
        }
    }
}

$content = include_template('signup.php', ['errors' => $errors]);

print($content);
