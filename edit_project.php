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
$sql_project_dropdown = "SELECT * FROM project WHERE userID = $userID AND id NOT IN (19) ORDER BY name ASC";
$sql_result = mysqli_query($con, $sql_project_dropdown);
// moving data into a multidimensional array
$projects_dropdown = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// getting all tasks from DB
$sql_task = "SELECT task.*, project.name as project_name
            FROM task as task
            JOIN project as project ON task.projectID = project.id
            WHERE task.userID = $userID";
$sql_task_result = mysqli_query($con, $sql_task);
$all_tasks = mysqli_fetch_all($sql_task_result, MYSQLI_ASSOC);

$no_tasks = "";

$project_id = $_POST['project'] ?? "";
$project_id = trim($project_id);
$project_id = intval($project_id);

$errors = [];
// if a user fills  in the "New Project Name" field
if (isset($_POST['rename'])) {

    $new_project_name = trim($_POST['rename']);
    $new_project_name = htmlspecialchars($new_project_name);
    $validate_empty = validateSize($new_project_name);

    // if there are no errors, update the project name in DB
    if (empty($validate_empty)) {
        $new_project_name = mb_strtolower($new_project_name);

        // checking if a project with this new name already exists
        $stmt = $con->prepare("SELECT * FROM project WHERE name = ?");
        $stmt->bind_param("s", $new_project_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $all_rows = $result->fetch_all(MYSQLI_ASSOC);
        $project_search = $all_rows;
        $stmt->close();

        if (empty($project_search)) {
            $sql = "UPDATE project SET name = ? WHERE id = ?";
            $stmt = db_get_prepare_stmt($con, $sql, [$new_project_name, $project_id]);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                header('Location: index.php');
            } else {
                $errors['rename'] = "Something went wrong. Try again later.";
            }
        } else {
            $errors['rename'] = "Project with this name already exists. Choose another name.";
        }

    } else {
        $errors['rename'] = $validate_empty;
    }
}


$delete_button = $_POST['delete'] ?? "";
// if a user clicks DELETE button
if ($delete_button) {

    if (isset($_POST['delete_tasks'])) {
        $sql = "DELETE FROM task WHERE projectID = ?";
        $stmt = db_get_prepare_stmt($con, $sql, [$project_id]);
        $result = mysqli_stmt_execute($stmt);
    } else {
        $sql = "UPDATE task SET projectID = 19 WHERE projectID = ?";
        $stmt = db_get_prepare_stmt($con, $sql, [$project_id]);
        $result = mysqli_stmt_execute($stmt);
    }

    $sql = "DELETE FROM project WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$project_id]);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header('Location: index.php');
    }
}

$content = include_template(
    'edit_project_templ.php',
    [
        'projects' => $projects_dropdown,
        'tasks' => $all_tasks,
        'no_tasks' => $no_tasks,
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
