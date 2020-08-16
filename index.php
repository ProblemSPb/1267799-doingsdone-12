<?php

require_once('helpers.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$title = "Doings Done";

$projects = ['Inbox', 'Study', 'Work', 'Chores', 'Car'];

$tasks = [
    ['Job Interview', '17.08.2020', 'Work', false],
    ['Do the tech test', '25.12.2020', 'Work', false],
    ['Finish the first task', '21.12.2020', 'Study', true],
    ['Meet the friend', '22.12.2020', 'Inbox', false],
    ['Buy cat\'s food', null, 'Chores', false],
    ['Cooking', null, 'Chores', false]
];

// function counts tasks in the project
function countTasks($tasks, $project_name)
{
    $counter = 0;
    foreach ($tasks as $task) {
        if ($task[2] === $project_name) {
            $counter++;
        }
    }
    return $counter;
}

// Чтобы получить количество дней между двумя датами, необходимо обе даты преобразовать в timestamp,
// вычислить количество секунд между ними, затем результат преобразовать в дни,
// разделив количество секунд на 86400 (количество секунд в одном дне, 60*60*24)

// function counts days difference between today and the task's due date
function count_time_diff($dueDate)
{
    $diff = 100;
    if ($dueDate !== null) {
        $diff = floor((strtotime($dueDate) - strtotime(today)) / 86400);
    }
    return $diff;
}


$content = include_template(
    'main.php',
    [
        'tasks' => $tasks,
        'projects' => $projects,
        'show_complete_tasks' => $show_complete_tasks
    ]
);

$layout = include_template(
    'layout.php',
    [
        'title' => $title,
        'projects' => $projects,
        'tasks' => $tasks,
        'content' => $content
    ]
);

print($layout);
