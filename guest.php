<?php

session_start();

require_once ('helpers.php');

// if user already logged in
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$content = include_template('guest_templ.php');

print($content);
