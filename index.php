<?php

session_start();

require_once('helpers.php');
require_once('db.php');
require_once('validation.php');
require_once ('vendor/autoload.php');

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

// show or hide completed tasks
$show_complete_tasks = 0;
if (isset($_GET['show_completed'])) {
    $show_complete_tasks = trim($_GET['show_completed']);
    $show_complete_tasks = intval($show_complete_tasks);
}

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
$all_tasks = mysqli_fetch_all($sql_task_result, MYSQLI_ASSOC);

// checking if checkbox is clicked => task status changed
if (isset($_GET['set_task_status']) && isset($_GET['status'])) {
    $task_id = trim($_GET['set_task_status']);
    $task_id = intval($task_id);
    $status = trim($_GET['status']);
    $status = intval($status);
    if (set_task_status($con, $task_id, $status, $userID)) {
        header("Location: /");
        exit;
    }
}

function get_task_rows($con, $userID, $projectID = 0, $filter = '', $query = '')
{
//    $rows = [];

    // search tasks by key words
    $query_text = "";
    if ($query) {
        $query_text = " AND MATCH(task.name) AGAINST('{$query}')";
    }

    // show tasks by categories "today"/"tomorrow"/"stale"
    $filter_text = "";
    if ($filter === '2') {
        $filter_text = " AND due_date = CURDATE()";
    } else if ($filter === '3') {
        $filter_text = " AND due_date IN (CURDATE(), CURDATE() + INTERVAL 1 DAY)";
    } else if ($filter === '4') {
        $filter_text = " AND due_date < CURDATE()";
    }

    // show tasks by project
    if ($projectID === '0') {
        $sql = "SELECT task.*, project.name as project_name FROM task as task
                JOIN project as project ON task.projectID = project.id
                WHERE task.userID = $userID" . $filter_text .$query_text;
    } elseif ($projectID !== 0) {
        $sql = "SELECT task.*, project.name as project_name FROM task as task
                JOIN project as project ON task.projectID = project.id
                WHERE task.userID = $userID AND task.projectID = " . $projectID . $filter_text .$query_text;
    } else {
        $sql = "SELECT task.*, project.name as project_name FROM task as task JOIN project as project ON task.projectID = project.id WHERE task.userID = $userID" . $filter_text .$query_text;
    }

    $sql_result = mysqli_query($con, $sql);
//    $rows = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

    return mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
}

// sanitizing user's input
$projectID = $_GET['id'] ?? 0;
$projectID = trim($projectID);
$projectID = intval($projectID);

$filter = $_GET['filter'] ?? 0;
$filter = trim($filter);
$filter = intval($filter);

$search = $_GET['search'] ?? "";
$search = trim($search);
$search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?? "";

$tasks = get_task_rows($con, $userID, $projectID, $filter, $search);

// if no tasks in the project
$no_tasks = '';
if (empty($tasks)) {
    $no_tasks = 'No tasks found';
}

$content = include_template(
    'main.php',
    [
        'tasks' => $tasks,
        'projects' => $projects,
        'show_complete_tasks' => $show_complete_tasks,
        'no_tasks' => $no_tasks,
        'search' => $search
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


// TODO: сделать подсветку табов при выборе фильтров
// TODO: POD prep stmnt - не везде сейчас
// TODO: выводить проекты в меню слева в алфавитном порядке

