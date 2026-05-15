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

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$gcash_number = trim($_POST['gcash_number'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

$facebook = trim($_POST['facebook_link'] ?? '');
$instagram = trim($_POST['instagram_link'] ?? '');
$x_link = trim($_POST['x_link'] ?? '');

$errors = [];


if ($fullname === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
    $errors[] = 'Full name, username, email, and password are required.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}

if ($gcash_number !== '' && !preg_match('/^09[0-9]{9}$/', $gcash_number)) {
    $errors[] = 'GCash number must be 11 digits and start with 09.';
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

$stmt = $conn->prepare('SELECT user_id FROM users WHERE username = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo '<p>Username or email already exists.</p>';
    echo '<p><a href="../index.php">Back</a></p>';
    exit;
}

$stmt->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);

$profileImagePath = '../assets/images/default-profile.png';

$insert = $conn->prepare("
    INSERT INTO users 
    (full_name, username, email, password, bio, profile_image, balance, total_earned, total_spent, total_sales, profile_views, is_verified, facebook_link, instagram_link, x_link, gcash_number)
    VALUES (?, ?, ?, ?, '', ?, 0, 0, 0, 0, 0, 0, ?, ?, ?, ?)
");

$insert->bind_param(
    'sssssssss',
    $fullname,
    $username,
    $email,
    $hashed,
    $profileImagePath,
    $facebook,
    $instagram,
    $x_link,
    $gcash_number
);

if ($insert->execute()) {
    $newUserId = $insert->insert_id;

    $userFolder = "../uploads/users/user_" . $newUserId;
    $profileFolder = $userFolder . "/profile";
    $artworksFolder = $userFolder . "/artworks";

    if (!is_dir($profileFolder)) {
        mkdir($profileFolder, 0777, true);
    }

    if (!is_dir($artworksFolder)) {
        mkdir($artworksFolder, 0777, true);
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $fileTmp = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExt, $allowed)) {
            $newFileName = "profile_" . $newUserId . "." . $fileExt;
            $uploadPath = $profileFolder . "/" . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $profileImagePath = "../uploads/users/user_" . $newUserId . "/profile/" . $newFileName;

                $update = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
                $update->bind_param('si', $profileImagePath, $newUserId);
                $update->execute();
                $update->close();
            }
        }
    }

    $_SESSION['user_id'] = $newUserId;
    $_SESSION['username'] = $username;

    header('Location: dashboard.php');
    exit;
} else {
    echo '<p>Registration failed: ' . htmlspecialchars($insert->error) . '</p>';
    echo '<p><a href="../index.php">Back</a></p>';
}

$insert->close();
$conn->close();
?>