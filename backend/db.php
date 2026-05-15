<?php
// Database configuration — adjust as needed for your XAMPP setup.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'comisgrid');

function db_connect()
{
    // Turn off mysqli exceptions/warnings for connection attempts
    mysqli_report(MYSQLI_REPORT_OFF);

    // Try connecting directly to the database
    $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli && !$mysqli->connect_error) {
        $mysqli->set_charset('utf8mb4');
        return $mysqli;
    }

    // If database doesn't exist, connect without selecting a DB and create it
    $tmp = @new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($tmp && !$tmp->connect_error) {
        $createDb = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($tmp->query($createDb)) {
            $tmp->close();
            // Try connecting again to the newly created database
            $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($mysqli && !$mysqli->connect_error) {
                $mysqli->set_charset('utf8mb4');
                return $mysqli;
            }
        } else {
            $tmp->close();
            return null;
        }
    }

    return null;
}

?>
