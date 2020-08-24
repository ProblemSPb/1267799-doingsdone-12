<?php

require_once('helpers.php');

// get field's value if POST sent
function getPOSTValue($value)
{
    return $_POST[$value] ?? '';
}

// get field's value if GET sent
function getGETValue($value)
{
    return $_GET[$value] ?? '';
}

// validates if text field not empty and is within the chars limits
function validateSize($field, $max)
{
    $validation = "";

    if (empty($field)) {
        $validation = 'Field cannot be empty';
    }

    if (strlen($field) > $max) {
        $validation = "It should not be more than " . $max . " characters.";
    }

    return $validation;
}

// validates the date
function validateDueDate($date)
{
    $validation = "";

    if (!(empty($date))) {
        if (!is_date_valid($date)) {
            $validation = "Date format should be YYYY-MM-DD";
        }
    }

    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    if ($dateTimeObj !== false && array_sum(date_get_last_errors()) === 0) {
        $expect_auc_end = (strtotime($date) - strtotime('now')) / 3600;
        if ($expect_auc_end < 0) {
            $validation = "Due date cannot be in the past";
        }
    }

    return $validation;
}

// checking if project exists when creating a task
function validateProject($value, $array)
{
    $validation = "";

    if (!(in_array($value, $array))) {
        $validation = "Select an existing project";
    }

    return $validation;
}

// validate email
function validateEmail($email)
{
    $validation = "";

    if (empty($email)) {
        $validation = "Email field cannot be empty";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validation = "It's not a valid email";
    }

    return $validation;
}

// validate pass
function validatePassword ($pass)
{
    $validation = "";

    if (empty($pass)) {
        $validation = "Password field cannot be empty";
    }

    if (strlen($pass) < 6) {
        $validation = "Password should have at least 6 characters including numbers, upper- and lowercase letters";
    }

    if (strlen($pass) > 30) {
        $validation = "Password should not be longer than 30 characters and have numbers, upper- and lowercase letters";
    }

    if (!((preg_match('/[A-Z]/', $pass)) && (preg_match('/[a-z]/', $pass)) && preg_match('/[0-9]/', $pass))) {
        $validation = "Password should have numbers, upper- and lowercase letters";
    }

    return $validation;
}
