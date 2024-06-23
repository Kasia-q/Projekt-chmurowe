<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'functions.php';
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_task'])) {
        $taskName = $_POST['task_name'];
        $dueDate = $_POST['due_date'];
        $priority = isset($_POST['priority']) ? 1 : 0;
        addTask($userId, $taskName, $dueDate, $priority);
    } elseif (isset($_POST['mark_done'])) {
        if (isset($_POST['done_tasks'])) {
            foreach ($_POST['done_tasks'] as $taskId) {
                markTaskAsDone($taskId);
            }
        }
    } elseif (isset($_POST['update_date'])) {
        if (isset($_POST['task_dates'])) {
            foreach ($_POST['task_dates'] as $taskId => $newDate) {
                updateTaskDate($taskId, $newDate);
            }
        }
    } elseif (isset($_POST['delete_tasks'])) {
        if (isset($_POST['delete_items'])) {
            foreach ($_POST['delete_items'] as $taskId) {
                deleteTask($taskId);
            }
        }
    }
}

$tasks = getTasks($userId);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Zadań</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #227182;
        }
        .main-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .priority {
            background-color: #f8d7da;
            color: #721c24;
        }
        .done {
            text-decoration: line-through;
            color: #6c757d;
        }
        .form-check-input.delete {
            background-color: #dc3545;
        }
        .form-check-input.done {
            background-color: #28a745;
        }
        .task-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .task-date {
            margin-left: auto;
            margin-right: 0;
        }
        .tasks-container, .add-task-container {
            width: 100%;
        }
        .tasks-container {
            flex-grow: 1;
        }
        .add-task-container {
            max-width: 300px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="main-container">
            <div class="header mb-4">
                <h1>Lista Zadań</h1>
                <div>
                    <span class="me-3">Zalogowany jako: <?php echo htmlspecialchars($username); ?></span>
                    <a href="logout.php" class="btn btn-secondary">Wyloguj</a>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-between w-100">
                <div class="add-task-container">
                    <form action="list.php" method="POST" class="mb-4">
                        <div class="mb-3">
                            <label for="task_name" class="form-label">Nazwa Zadania</label>
                            <input type="text" class="form-control" id="task_name" name="task_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Data Wykonania</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required style="max-width: 200px;">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="priority" name="priority">
                            <label class="form-check-label" for="priority">Priorytet</label>
                        </div>
                        <button type="submit" name="add_task" class="btn btn-primary">Dodaj Zadanie</button>
                    </form>
                </div>

                <div class="tasks-container">
                    <form action="list.php" method="POST">
                        <ul class="list-group">
                            <?php while ($task = $tasks->fetch_assoc()) { 
                                $class = $task['priority'] ? 'list-group-item priority' : 'list-group-item';
                                if ($task['done']) {
                                    $class .= ' done';
                                }
                                ?>
                                <li class="<?php echo $class; ?> task-container">
                                    <div>
                                        <input type="checkbox" name="delete_items[]" value="<?php echo $task['id']; ?>" class="form-check-input me-2 delete" title="Usuń zadanie">
                                        <input type="checkbox" name="done_tasks[]" value="<?php echo $task['id']; ?>" class="form-check-input me-2 done" title="Oznacz jako zrobione" <?php if ($task['done']) echo 'checked disabled'; ?>>
                                        <?php echo htmlspecialchars($task['task_name']); ?>
                                    </div>
                                    <input type="date" name="task_dates[<?php echo $task['id']; ?>]" value="<?php echo $task['due_date']; ?>" class="form-control form-control-sm w-auto d-inline task-date" <?php if ($task['done']) echo 'disabled'; ?>>
                                </li>
                            <?php } ?>
                        </ul>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" name="update_date" class="btn btn-primary me-2">Zaktualizuj Daty</button>
                            <button type="submit" name="mark_done" class="btn btn-success me-2">Oznacz jako Zrobione</button>
                            <button type="submit" name="delete_tasks" class="btn btn-danger">Usuń Wybrane</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
