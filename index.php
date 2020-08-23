<?php

session_start();

require_once('helpers.php');
require_once('db.php');

$title = "Doings Done";
$user_name = "";
$userID = 0;

if (!empty($_SESSION)) {
    $userID = $_SESSION['user']['user_id'];
    $user_name = $_SESSION['user']['name'];
} else {
    header("Location: guest.php");
    exit();
}
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// getting projects for left side menu from DB
$sql_project = "SELECT * FROM project WHERE userID = $userID";
/** @var $con source db.php */
$sql_result = mysqli_query($con, $sql_project);
// moving data into a multidimensional array
$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// getting tasks from DB
$sql_task = "SELECT task.*, project.name as project_name
            FROM task as task
            JOIN project as project ON task.projectID = project.id
            WHERE task.userID = $userID";
$sql_task_result = mysqli_query($con, $sql_task);
$tasks = mysqli_fetch_all($sql_task_result, MYSQLI_ASSOC);

// if a project is selected, respective tasks are shown
if (isset($_GET['id']) && $_GET['id']) {
    $projectID = mysqli_real_escape_string($con, intval($_GET['id']));
    $sql_task .= " AND projectID = $projectID ";
}
$sql_task_result = mysqli_query($con, $sql_task);

// if no tasks in the project
$no_tasks = '';
if (!mysqli_num_rows($sql_task_result)) {
    // http_response_code(404);
    // exit();
    $no_tasks = 'No tasks found';
}
$tasks_by_project = mysqli_fetch_all($sql_task_result, MYSQLI_ASSOC);


// function counts tasks in the project
function countTasks($tasks, $project_name)
{
    $counter = 0;

    foreach ($tasks as $key => $value) {
        if ($value['project_name'] === $project_name) {
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
        'show_complete_tasks' => $show_complete_tasks,
        'tasks_by_project' => $tasks_by_project,
        'no_tasks' => $no_tasks
    ]
);

$layout = include_template(
    'layout.php',
    [
        'title' => $title,
        'projects' => $projects,
        'tasks' => $tasks,
        'content' => $content,
        'user_name' => $user_name
    ]
);

print($layout);
