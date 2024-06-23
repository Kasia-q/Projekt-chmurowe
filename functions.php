<?php
function connectDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "task_manager";  // Zmieniono nazwę bazy danych

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function registerUser($username, $password) {
    $conn = connectDatabase();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

function loginUser($username, $password) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            return $userId;
        }
    }
    $stmt->close();
    $conn->close();
    return false;
}

function addTask($userId, $taskName, $dueDate, $priority) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name, due_date, priority) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $userId, $taskName, $dueDate, $priority);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function getTasks($userId) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("SELECT id, task_name, due_date, priority, done FROM tasks WHERE user_id = ? ORDER BY priority DESC, due_date ASC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    return $result;
}

function markTaskAsDone($taskId) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("UPDATE tasks SET done = 1 WHERE id = ?");
    $stmt->bind_param("i", $taskId);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function updateTaskDate($taskId, $newDate) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("UPDATE tasks SET due_date = ? WHERE id = ?");
    $stmt->bind_param("si", $newDate, $taskId);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function deleteTask($taskId) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $taskId);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return false;
    }

    $stmt->close();
    $conn->close();
    return true;
}
?>
