<?php

session_start();

require_once ('helpers.php');
require_once ('db.php');
require_once ('validation.php');

//////////////////////
/// Layout data
/////////////////////
$title = "Doings Done";
$user_name = "";
$userID = 0;

if (UserHelper::isLoggedIn()) {
    $userID = $_SESSION['user']['user_id'];
    $user_name = $_SESSION['user']['name'];

} else {
    header("Location: guest.php");
    exit();
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

//////////////////////
/// Layout data end
/////////////////////

$errors = [];

if (isset($_POST['name'])) {
    $validate_empty = validateSize($_POST['name']);

    if (empty($validate_empty)) {
        // if project with this name already exists
        $search = ($_POST['name']);
        $search = mb_strtolower($search);

        $stmt = $con->prepare("SELECT * FROM project WHERE name = ?");
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $all_rows = $result->fetch_all(MYSQLI_ASSOC);
        $project_search = $all_rows;
        $stmt->close();

        if (empty($project_search)) {
            $sql = "INSERT INTO project (name, userID) VALUES  (?, ?)";
            $stmt = db_get_prepare_stmt($con, $sql, [$search, $userID]);
            $result = mysqli_stmt_execute($stmt);

            if($result) {
                header('Location: index.php');
            }
        } else {
            $errors['name'] = "Project with this name already exists";
        }
    } else {
        $errors['name'] = $validate_empty;
    }
}

$content = include_template('add_project_templ.php',
    [
        'errors' => $errors
    ]
);

$layout = include_template('layout.php',
    [
        'title' => $title,
        'projects' => $projects,
        'all_tasks' => $all_tasks,
        'content' => $content,
        'user_name' => $user_name
    ]
);

print ($layout);
