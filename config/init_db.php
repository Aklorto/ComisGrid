<?php
require_once __DIR__ . '/db.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$createDb = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$mysqli->query($createDb)) {
    die('Create DB failed: ' . $mysqli->error);
}

$mysqli->select_db(DB_NAME);

$createTable = <<<SQL
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  contact VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;

if (!$mysqli->query($createTable)) {
    die('Create table failed: ' . $mysqli->error);
}

echo "Database and table are ready.\n";
echo "Database: " . DB_NAME . "\n";
echo "Run this script from the browser or CLI once to initialize the DB.";

?>
