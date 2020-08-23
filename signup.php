<?php

session_start();

require_once('helpers.php');
require_once('db.php');
require_once('validation.php');

// if user already logged in
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rules = [
        'email' => validateEmail($_POST['email']),
        'password' => validatePassword($_POST['password']),
        'name' => validateSize($_POST['name'], 20)
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $errors[$key] = $rules[$key];
        }
    }

    // if user with this email alredy exists in DB
    $stmt = $con->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $stmt_result = mysqli_stmt_get_result($stmt);
    $stmt->close();

    if (mysqli_num_rows($stmt_result)) {
        $errors['email'] = "This email is already registered";
    }

    $errors = array_filter($errors);

    if (empty($errors)) {
        $email = $_POST['email'];
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $name = $_POST['name'];

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
