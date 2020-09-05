<?php

session_start();

require_once('helpers.php');
require_once('db.php');
require_once('validation.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$title = "Doings Done";
$user_name = "";
$userID = 0;

if (UserHelper::isLoggedIn()) {
    $userID = intval($_SESSION['user']['user_id']);
    $user_name = htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8');

} else {
    header("Location: guest.php");
    exit();
}

// getting projects for left side menu from DB
$sql_project = "SELECT * FROM project WHERE userID = $userID ORDER BY name ASC";
$sql_result = mysqli_query($con, $sql_project);
// moving data into a multidimensional array
$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// getting projects id for validation
$sql_project_id = "SELECT id FROM project WHERE userID = $userID";
$sql_result = mysqli_query($con, $sql_project_id);
// moving data into a multidimensional array
$projects_id = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// getting all tasks from DB
$sql_task = "SELECT task.*, project.name as project_name
            FROM task as task
            JOIN project as project ON task.projectID = project.id
            WHERE task.userID = $userID";
$sql_task_result = mysqli_query($con, $sql_task);
$all_tasks = mysqli_fetch_all($sql_task_result, MYSQLI_ASSOC);

$no_tasks = "";

$content = include_template(
    'edit_project_templ.php',
    [
        'projects' => $projects,
        'tasks' => $all_tasks,
        'no_tasks' => $no_tasks
    ]
);

$layout = include_template(
    'layout.php',
    [
        'title' => $title,
        'projects' => $projects,
        'all_tasks' => $all_tasks,
        'content' => $content,
        'user_name' => $user_name
    ]
);

print($layout);
