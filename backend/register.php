<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../index.php');
	exit;
}

require_once __DIR__ . '/db.php';

$mysqli = db_connect();
if (!$mysqli) {
	die('Database connection failed. Please run init_db.php or check DB credentials.');
}

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$contact = trim($_POST['contact'] ?? '');

$errors = [];
if ($fullname === '' || $username === '' || $email === '' || $password === '' || $confirm === '' || $contact === '') {
	$errors[] = 'All fields are required.';
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$errors[] = 'Invalid email address.';
}

if ($contact !== '' && !preg_match('/^\+?[0-9 \-]{7,20}$/', $contact)) {
	$errors[] = 'Invalid contact number format.';
}

if ($password !== $confirm) {
	$errors[] = 'Passwords do not match.';
}

if (!empty($errors)) {
	foreach ($errors as $err) {
		echo '<p>' . htmlspecialchars($err) . '</p>';
	}
	echo '<p><a href="../index.php">Back</a></p>';
	exit;
}

// Check for existing username or email
$stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
if (!$stmt) {
	die('Prepare failed: ' . $mysqli->error);
}
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	echo '<p>Username or email already exists.</p>';
	echo '<p><a href="../index.php">Back</a></p>';
	$stmt->close();
	exit;
}
$stmt->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);

$ins = $mysqli->prepare('INSERT INTO users (fullname, username, email, password, contact) VALUES (?, ?, ?, ?, ?)');
if (!$ins) {
	die('Prepare failed: ' . $mysqli->error);
}
$ins->bind_param('sssss', $fullname, $username, $email, $hashed, $contact);
if ($ins->execute()) {
	echo '<h1>Registration Successful</h1>';
	echo '<p>Welcome, ' . htmlspecialchars($fullname) . '.</p>';
	echo '<p>Contact number: ' . htmlspecialchars($contact) . '</p>';
	echo '<p><a href="../index.php">Return to login</a></p>';
} else {
	echo '<p>Failed to register: ' . htmlspecialchars($ins->error) . '</p>';
	echo '<p><a href="../index.php">Back</a></p>';
}

$ins->close();
$mysqli->close();
?>
