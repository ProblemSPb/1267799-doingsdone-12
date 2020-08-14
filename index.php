<?php

require_once('helpers.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$title = "Doings Done";

$projects = ['Inbox', 'Study', 'Work', 'Chores', 'Car'];

$tasks = [
    ['Job Interview', '01.12.2019', 'Work', false],
    ['Do the tech test', '25.12.2019', 'Work', false],
    ['Finish the first task', '21.12.2019', 'Study', true],
    ['Meet the friend', '22.12.2019', 'Inbox', false],
    ['Buy cat\'s food', null, 'Chores', false],
    ['Cooking', null, 'Chores', false]
];

// функция подсчета количества тасок в проекте
function countTasks($tasks, $project_name) {
    $counter = 0;
    foreach($tasks as $task){
        if($task[2] === $project_name){
            $counter++;
        }
    }
    return $counter;
}

$content = include_template(
    'main.php',
    [
        'tasks' => $tasks,
        'projects' => $projects
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