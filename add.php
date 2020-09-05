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


// link to a file is empty unless an actual file is added to a new task
$file = NULL;
// due date empty if not assigned
$due_date = NULL;

$errors = [];
// checking if form is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // sanitizing user's inputs
    $name = trim($_POST['name']);
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $project_id = trim($_POST['project']);
    $project_id = intval($project_id);

    if (isset($_POST['date'])) {
        $due_date = trim($_POST['date']);
        $due_date = htmlspecialchars($due_date, ENT_QUOTES, 'UTF-8');
    }

    // validation rules
    $rules = [
//        'name' => validateSize($_POST['name'], 50),
        'name' => validateSize($name),
        'date' => validateDueDate($due_date),
        'projects' => validateProject($project_id, $projects_id)
    ];

    // filling in array with error if any
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $errors[$key] = $rules[$key];
        }
    }

    // validating and adding file if uploaded
    if (isset($_FILES['file'])) {
        if ($_FILES['file']['size'] > 200000) {
            $errors['file'] = "File size should not exceed 200KB";
        } else {
            $file_name = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES, 'UTF-8');
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' . $file_name;
            $link_file = $file_url;

            $result = move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);

            if ($result) {
                $file = $file_url;
            }

        }
    }

    $errors = array_filter($errors);

    if (empty($errors)) {

        $sql = "INSERT INTO task (name, file, due_date, userID, projectID)
        VALUES  (?, ?, ?, ?, ?)";
        $stmt = db_get_prepare_stmt($con, $sql, [$name, $file, $due_date, $userID, $project_id]);
        $result = mysqli_stmt_execute($stmt);

        if($result) {
            header('Location: index.php');
        }
    }
}

$content = include_template(
    'add_task_templ.php',
    [
        'projects' => $projects,
        'errors' => $errors
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
