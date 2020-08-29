<?php

require_once('helpers.php');
require_once('vendor/autoload.php');

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationExceptionInterface;
use Respect\Validation\Exceptions\NestedValidationException;

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
function validateSize($field)
{
    $validation = '';
    $validator = v::stringType()->length(1, 40);
    if (!$validator->validate($field)) {
        $validation = "The field cannot be empty and have more than 40 chars.";
    }

    return $validation;
}

// validates the date
function validateDueDate($date)
{
    $validation = "";

    if (!(empty($date))) {
//        if (!is_date_valid($date)) {
        if (!v::date('Y-m-d')) {
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
        $validation = "Select one of the existing projects";
    }

    return $validation;
}

// validate email
function validateEmail($email)
{
    $validation = "";
    if (!v::email()->validate($email)) {
        $validation = "It's not a valid email";
    }

    return $validation;
}

// validate password
function validatePassword($pass)
{
    $validation = "";
    $validation_array = [];

    $validator = v::length(6, 30)->noWhitespace()->regex('/[A-Z]/')->regex( '/[a-z]/')->regex('/[0-9]/');

    try {
        $validator->assert($pass);
    } catch (NestedValidationException $exception) {
        $validation_array =
            $exception->getMessages([
                'length' => 'Password must not have less than 6 and more than 30 chars.',
                'noWhitespace' => 'Password should not contain spaces.',
                'regex' => 'Password should have numbers, upper- and lowercase letters.'
            ]);
    }

    // in case we want more than 1 rule of validation
    foreach ($validation_array as $key => $value) {
        $validation = $validation . " " . $value;
    }

    return $validation;
}
