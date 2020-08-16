<?php

require_once('helpers.php');
require_once('db.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$title = "Doings Done";

// getting projects from DB
$sql_project = "SELECT * FROM project";
$sql_result = mysqli_query($con, $sql_project);
// moving data into a multidimensional array
$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// getting tasks from DB
$sql_task = "SELECT task.*, project.name as project_name
            FROM task as task
            JOIN project as project ON task.projectID = project.id";
$sql_task_result = mysqli_query($con, $sql_task);
$tasks = mysqli_fetch_all($sql_task_result, MYSQLI_ASSOC);

// function counts tasks in the project
function countTasks($tasks, $project_name)
{
    $counter = 0;
    // foreach ($tasks as $task) {
    //     if ($task[2] === $project_name) {
    //         $counter++;
    //     }
    // }

    foreach($tasks as $key => $value) {
        if($value['project_name'] === $project_name){
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
