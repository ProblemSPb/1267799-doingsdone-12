<?php

session_start();

require_once('helpers.php');
require_once('db.php');
require_once('validation.php');

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
$sql_result = mysqli_query($con, $sql_project);
// moving data into a multidimensional array
$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// getting all tasks from DB
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
$tasks_filtered = mysqli_fetch_all($sql_task_result, MYSQLI_ASSOC);

// searching tasks by key words
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    $stmt = $con->prepare("SELECT * FROM task WHERE MATCH(name) AGAINST (?)");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $all_rows = $result->fetch_all(MYSQLI_ASSOC);
    $tasks_filtered = $all_rows;
    $stmt->close();

    if (empty($tasks_filtered)) {
        $no_tasks = 'No tasks found';
    }
}

$content = include_template(
    'main.php',
    [
        'tasks' => $tasks,
        'projects' => $projects,
        'show_complete_tasks' => $show_complete_tasks,
        'tasks_filtered' => $tasks_filtered,
        'no_tasks' => $no_tasks,
        'search' => $search
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
