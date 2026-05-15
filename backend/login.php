<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

$conn = db_connect();

if (!$conn) {
    die('Database connection failed.');
}

$loginInput = trim($_POST['login_input'] ?? '');
$password = $_POST['password'] ?? '';

if ($loginInput === '' || $password === '') {
    echo '<p>Email/username and password are required.</p>';
    echo '<p><a href="../index.php">Back</a></p>';
    exit;
}

$stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ? OR username = ? LIMIT 1");
$stmt->bind_param('ss', $loginInput, $loginInput);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        header('Location: dashboard.php');
        exit;
    }
}

echo '<p>Invalid username/email or password.</p>';
echo '<p><a href="../index.php">Back</a></p>';

$stmt->close();
$conn->close();
?>